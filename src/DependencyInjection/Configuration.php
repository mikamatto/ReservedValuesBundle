<?php

namespace Mikamatto\ReservedValuesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('reserved_values');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('bypass_role')
                    ->defaultValue('ROLE_ADMIN')
                    ->info('The minimum role required to bypass reserved values validation')
                ->end()
            ->end()
            ->useAttributeAsKey('key')
            ->arrayPrototype()
                ->children()
                    ->arrayNode('exact')
                        ->scalarPrototype()->end()
                    ->end()
                    ->arrayNode('patterns')
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}