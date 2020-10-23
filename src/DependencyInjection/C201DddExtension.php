<?php

namespace C201\Ddd\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-04-22
 */
class C201DddExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../resources/config')
        );
        $loader->load('services.yml');

        $definition = $container->getDefinition('c201_ddd.events.event_store');
        $definition->replaceArgument(4, $config['use_event_store']);
    }

    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('doctrine_migrations', [
            'migrations_paths' => [
                'C201\Ddd\Events\Infrastructure\DoctrineMigrations' => __DIR__. '/../../../ddd-doctrine-bridge/src/Events/Infrastructure/DoctrineMigrations',
            ]
        ]);
    }
}
