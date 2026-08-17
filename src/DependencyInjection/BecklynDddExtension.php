<?php declare(strict_types=1);

namespace Becklyn\Ddd\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author Marko Vujnovic <mv@becklyn.com>
 *
 * @since  2020-04-22
 */
class BecklynDddExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container) : void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../../resources/config')
        );
        $loader->load('services.yml');

        $definition = $container->getDefinition('becklyn_ddd.events.event_store');
        $definition->replaceArgument(4, $config['use_event_store']);
    }

    public function prepend(ContainerBuilder $container) : void
    {
        $container->prependExtensionConfig('doctrine_migrations', [
            'migrations_paths' => [
                'Becklyn\\Ddd\\Events\\Infrastructure\\DoctrineMigrations' => __DIR__ . '/../../../ddd-doctrine-bridge/src/Events/Infrastructure/DoctrineMigrations',
            ],
        ]);

        // Declares the two Messenger buses the command/event bus adapters are
        // wired to, so consuming applications need no messenger configuration of
        // their own. Both are synchronous: with no transport routing configured,
        // Messenger handles a dispatched message immediately and depth-first,
        // which is the behaviour SimpleBus had with
        // `finishes_command_before_handling_next: false`.
        //
        // allow_no_handlers is required on the event bus: a domain event with no
        // subscriber is normal, and Messenger would otherwise throw
        // NoHandlerForMessageException.
        // default_bus is mandatory as soon as more than one bus is declared, and
        // it is what MessageBusInterface autowires to. It is prepended, so an
        // application that sets its own framework.messenger.default_bus wins.
        $container->prependExtensionConfig('framework', [
            'messenger' => [
                'default_bus' => 'becklyn_ddd.messenger.command_bus',
                'buses' => [
                    'becklyn_ddd.messenger.command_bus' => [
                        'default_middleware' => [
                            'enabled' => true,
                        ],
                    ],
                    'becklyn_ddd.messenger.event_bus' => [
                        'default_middleware' => [
                            'enabled' => true,
                            'allow_no_handlers' => true,
                        ],
                    ],
                ],
            ],
        ]);
    }
}
