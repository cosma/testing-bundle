<?php

/**
 * This file is part of the "cosma/testing-bundle" project
 *
 * (c) Cosmin Voicu<cosmin.voicu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 11/07/14
 * Time: 23:33
 */

namespace Cosma\Bundle\TestingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('testing');

        $rootNode
            ->children()
                ->arrayNode('solarium')
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('host')->defaultValue('127.0.0.1')->end()
                        ->scalarNode('port')->defaultValue(8983)->end()
                        ->scalarNode('path')->defaultValue('/solr')->end()
                        ->scalarNode('core')->end()
                        ->scalarNode('timeout')->defaultValue(5)->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
