<?php

namespace Airos\AutomagicKernelBundle\Configuration;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class BundleConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('autoload');

        $rootNode
            ->children()
                ->arrayNode('bundles')
                    ->prototype('array')
                    ->children()
                        ->scalarNode('fqcn')->end()
                        ->variableNode('env')
                            ->validate()
                                ->ifString()
                                    ->then(function ($v) { return array($v); })->end()
                            ->end()
                        ->booleanNode('kernel')->defaultFalse()->end()
                        ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}