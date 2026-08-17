<?php declare(strict_types=1);

namespace Becklyn\Ddd\Tests\DependencyInjection\Compiler;

use Becklyn\Ddd\DependencyInjection\Compiler\RegisterHandlersPass;
use Becklyn\Ddd\Tests\DependencyInjection\Compiler\Fixtures\ExampleCommand;
use Becklyn\Ddd\Tests\DependencyInjection\Compiler\Fixtures\ExampleCommandHandler;
use Becklyn\Ddd\Tests\DependencyInjection\Compiler\Fixtures\ExampleEvent;
use Becklyn\Ddd\Tests\DependencyInjection\Compiler\Fixtures\ExampleQuery;
use Becklyn\Ddd\Tests\DependencyInjection\Compiler\Fixtures\MultiMethodSubscriber;
use Becklyn\Ddd\Tests\DependencyInjection\Compiler\Fixtures\OtherExampleEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Definition;

class RegisterHandlersPassTest extends TestCase
{
    private ContainerBuilder $container;

    protected function setUp() : void
    {
        $this->container = new ContainerBuilder();
    }

    /**
     * @return array<int, array{bus: string, handles: string, method: string}>
     */
    private function messengerTags(string $serviceId) : array
    {
        return $this->container->getDefinition($serviceId)->getTag('messenger.message_handler');
    }

    public function testCommandHandlerIsRegisteredOnTheCommandBus() : void
    {
        $this->container->setDefinition(ExampleCommandHandler::class, (new Definition(ExampleCommandHandler::class))
            ->addTag('command_handler', ['register_public_methods' => true]));

        (new RegisterHandlersPass())->process($this->container);

        self::assertSame([[
            'bus' => 'becklyn_ddd.messenger.command_bus',
            'handles' => ExampleCommand::class,
            'method' => 'handle',
        ]], $this->messengerTags(ExampleCommandHandler::class));
    }

    public function testMethodsTypedAgainstTheCommandInterfaceItselfAreNotRegistered() : void
    {
        // ExampleCommandHandler::executeTemplate(Command $command) mirrors the
        // abstract template method every becklyn command handler inherits. It must
        // not become a handler for the Command interface.
        $this->container->setDefinition(ExampleCommandHandler::class, (new Definition(ExampleCommandHandler::class))
            ->addTag('command_handler', ['register_public_methods' => true]));

        (new RegisterHandlersPass())->process($this->container);

        $methods = \array_column($this->messengerTags(ExampleCommandHandler::class), 'method');
        self::assertNotContains('executeTemplate', $methods);
    }

    public function testSetterInjectionAndQueryMethodsAreNotRegistered() : void
    {
        $this->container->setDefinition(ExampleCommandHandler::class, (new Definition(ExampleCommandHandler::class))
            ->addTag('command_handler', ['register_public_methods' => true]));

        (new RegisterHandlersPass())->process($this->container);

        $methods = \array_column($this->messengerTags(ExampleCommandHandler::class), 'method');
        self::assertNotContains('setEventBus', $methods, 'a #[Required] setter must not be dispatched to');
        self::assertNotContains('execute', $methods, 'a query handler method must not be dispatched to');
        self::assertNotContains('noParameters', $methods);
    }

    public function testEventSubscriberWithSeveralHandlerMethodsIsRegisteredOncePerMethod() : void
    {
        $this->container->setDefinition(MultiMethodSubscriber::class, (new Definition(MultiMethodSubscriber::class))
            ->addTag('event_subscriber', ['register_public_methods' => true]));

        (new RegisterHandlersPass())->process($this->container);

        $tags = $this->messengerTags(MultiMethodSubscriber::class);
        \usort($tags, static fn (array $a, array $b) => $a['method'] <=> $b['method']);

        self::assertSame([
            ['bus' => 'becklyn_ddd.messenger.event_bus', 'handles' => ExampleEvent::class, 'method' => 'handle'],
            ['bus' => 'becklyn_ddd.messenger.event_bus', 'handles' => OtherExampleEvent::class, 'method' => 'handleOther'],
        ], $tags);
    }

    public function testQueryClassesAreNotTreatedAsMessages() : void
    {
        self::assertFalse(\is_subclass_of(ExampleQuery::class, \Becklyn\Ddd\Commands\Domain\Command::class));
    }

    public function testMethodTagAttributeRestrictsRegistrationToThatMethod() : void
    {
        $this->container->setDefinition(MultiMethodSubscriber::class, (new Definition(MultiMethodSubscriber::class))
            ->addTag('event_subscriber', ['method' => 'handleOther']));

        (new RegisterHandlersPass())->process($this->container);

        self::assertSame([[
            'bus' => 'becklyn_ddd.messenger.event_bus',
            'handles' => OtherExampleEvent::class,
            'method' => 'handleOther',
        ]], $this->messengerTags(MultiMethodSubscriber::class));
    }

    public function testAbstractDefinitionsAreRejected() : void
    {
        $this->container->setDefinition(ExampleCommandHandler::class, (new Definition(ExampleCommandHandler::class))
            ->setAbstract(true)
            ->addTag('command_handler', ['register_public_methods' => true]));

        // Matches Symfony's own MessengerPass: an abstract tagged service is an
        // error, not something to silently ignore.
        $this->expectException(InvalidArgumentException::class);

        (new RegisterHandlersPass())->process($this->container);
    }

    public function testUntaggedServicesAreLeftAlone() : void
    {
        $this->container->setDefinition(ExampleCommandHandler::class, new Definition(ExampleCommandHandler::class));

        (new RegisterHandlersPass())->process($this->container);

        self::assertSame([], $this->messengerTags(ExampleCommandHandler::class));
    }
}
