<?php

namespace Lsw\GuzzleBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * Defines the configuration options for the Guzzle object
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('lsw_guzzle');
        $rootNode->append($this->addclientsSection());

        return $treeBuilder;
    }

    /**
     * Configure the "lsw_guzzle.clients" section
     *
     * @return ArrayNodeDefinition
     */
    private function addclientsSection()
    {
        $tree = new TreeBuilder();
        $node = $tree->root('clients');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('variable')
            ->end()
        ->end();

        return $node;
    }

}