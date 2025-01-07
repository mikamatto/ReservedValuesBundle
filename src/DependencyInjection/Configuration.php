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
                ->arrayNode('bypass_roles')
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function ($v) { return [$v]; })
                    ->end()
                    ->prototype('scalar')->end()
                    ->defaultValue(['ROLE_ADMIN'])
                    ->info('The roles that are allowed to bypass reserved values validation')
                ->end()
                ->arrayNode('keys')
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
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}