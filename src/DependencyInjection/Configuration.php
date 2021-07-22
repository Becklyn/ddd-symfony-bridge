<?php

namespace Becklyn\Ddd\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Marko Vujnovic <mv@becklyn.com>
 * @since  2020-04-23
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('becklyn_ddd');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->booleanNode('use_event_store')
                    ->defaultTrue()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
