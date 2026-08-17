<?php declare(strict_types=1);

namespace Becklyn\Ddd\DependencyInjection\Compiler;

use Becklyn\Ddd\Commands\Domain\Command;
use Becklyn\Ddd\Events\Domain\DomainEvent;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * Translates the SimpleBus service tags an application already uses --
 * `command_handler` and `event_subscriber` -- into Symfony Messenger's
 * `messenger.message_handler` tags.
 *
 * This exists so that dropping simple-bus/symfony-bridge (which caps symfony/*
 * at ^6.0 and has been unmaintained since 2021) does not require touching every
 * handler class in a consuming application.
 *
 * For each tagged service, every public method whose first parameter is typed
 * against a concrete Command (for `command_handler`) or DomainEvent (for
 * `event_subscriber`) becomes one handler registration. Methods typed against
 * the Command/DomainEvent interfaces themselves are skipped, as are methods with
 * no parameters or with an unrelated first parameter -- that keeps
 * infrastructure methods such as an `#[Required] setEventBus(EventBus $bus)`
 * setter, or a query handler's `execute(SomeQuery $query)`, out of the bus.
 *
 * Honours the `method` tag attribute when present; otherwise all public methods
 * are considered, which matches SimpleBus's `register_public_methods: true`.
 */
class RegisterHandlersPass implements CompilerPassInterface
{
    private const COMMAND_TAG = 'command_handler';
    private const EVENT_TAG = 'event_subscriber';
    private const MESSENGER_TAG = 'messenger.message_handler';

    public function __construct(
        private string $commandBus = 'becklyn_ddd.messenger.command_bus',
        private string $eventBus = 'becklyn_ddd.messenger.event_bus',
    ) {
    }

    public function process(ContainerBuilder $container) : void
    {
        $this->registerTag($container, self::COMMAND_TAG, Command::class, $this->commandBus);
        $this->registerTag($container, self::EVENT_TAG, DomainEvent::class, $this->eventBus);
    }

    /**
     * @param class-string $messageInterface
     */
    private function registerTag(ContainerBuilder $container, string $tag, string $messageInterface, string $bus) : void
    {
        foreach ($container->findTaggedServiceIds($tag, true) as $serviceId => $tagAttributes) {
            $definition = $container->getDefinition($serviceId);
            $class = $this->resolveClass($container, $definition, $serviceId);

            if (null === $class || !\class_exists($class)) {
                continue;
            }

            foreach ($tagAttributes as $attributes) {
                $methods = isset($attributes['method'])
                    ? [$attributes['method']]
                    : $this->publicMethods($class);

                foreach ($methods as $method) {
                    $messages = $this->messagesHandledBy($class, $method, $messageInterface);

                    foreach ($messages as $message) {
                        $definition->addTag(self::MESSENGER_TAG, [
                            'bus' => $bus,
                            'handles' => $message,
                            'method' => $method,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * @param class-string $class
     *
     * @return string[]
     */
    private function publicMethods(string $class) : array
    {
        $methods = [];

        foreach ((new \ReflectionClass($class))->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isStatic() || $method->isConstructor() || $method->isDestructor()) {
                continue;
            }

            $methods[] = $method->getName();
        }

        return $methods;
    }

    /**
     * Returns the concrete message class the given method handles, if any.
     *
     * @param class-string $class
     * @param class-string $messageInterface
     *
     * @return class-string[]
     */
    private function messagesHandledBy(string $class, string $method, string $messageInterface) : array
    {
        try {
            $reflectionMethod = new \ReflectionMethod($class, $method);
        } catch (\ReflectionException) {
            return [];
        }

        $parameters = $reflectionMethod->getParameters();

        if (0 === \count($parameters)) {
            return [];
        }

        $type = $parameters[0]->getType();
        $candidates = [];

        // A union type such as `handle(FooHappened|BarHappened $event)` registers
        // the method once per member of the union.
        if ($type instanceof \ReflectionUnionType) {
            foreach ($type->getTypes() as $member) {
                if ($member instanceof \ReflectionNamedType) {
                    $candidates[] = $member->getName();
                }
            }
        } elseif ($type instanceof \ReflectionNamedType) {
            $candidates[] = $type->getName();
        }

        $messages = [];

        foreach ($candidates as $candidate) {
            if ($candidate === $messageInterface) {
                // Typed against the interface itself, e.g. the abstract
                // `execute(Command $command)` template method. Not a message.
                continue;
            }

            if (!\class_exists($candidate) && !\interface_exists($candidate)) {
                continue;
            }

            if (!\is_subclass_of($candidate, $messageInterface)) {
                continue;
            }

            if ((new \ReflectionClass($candidate))->isAbstract()) {
                continue;
            }

            $messages[] = $candidate;
        }

        return $messages;
    }

    private function resolveClass(ContainerBuilder $container, Definition $definition, string $serviceId) : ?string
    {
        $class = $definition->getClass();

        if (null === $class) {
            // Autoconfigured services whose id is the FQCN.
            return \class_exists($serviceId) ? $serviceId : null;
        }

        try {
            $resolved = $container->getParameterBag()->resolveValue($class);
        } catch (InvalidArgumentException) {
            return null;
        }

        return \is_string($resolved) ? $resolved : null;
    }
}
