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

use Cosma\Bundle\TestingBundle\ORM\DoctrineORMSchemaTool;
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
        $rootNode    = $treeBuilder->root('cosma_testing');

        $rootNode
            ->children()
                ->scalarNode('fixture_directory')->defaultValue('Fixture')->end()
                ->scalarNode('tests_directory')->defaultValue('Tests')->end()
                ->arrayNode('doctrine')
                    ->canBeUnset()
                    ->children()
                        ->enumNode('cleaning_strategy')
                            ->defaultValue(DoctrineORMSchemaTool::DOCTRINE_CLEANING_TRUNCATE)
                            ->values(
                                [
                                    DoctrineORMSchemaTool::DOCTRINE_CLEANING_TRUNCATE,
                                    DoctrineORMSchemaTool::DOCTRINE_CLEANING_DROP
                                ]
                            )
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('solarium')
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('host')->defaultValue('127.0.0.1')->end()
                        ->scalarNode('port')->defaultValue(8080)->end()
                        ->scalarNode('path')->defaultValue('/solr')->end()
                        ->scalarNode('core')->defaultValue('test')->end()
                        ->scalarNode('timeout')->defaultValue(5)->end()
                    ->end()
                ->end()
                ->arrayNode('elastica')
                    ->canBeUnset()
                    ->children()
                    ->scalarNode('host')->defaultValue('127.0.0.1')->end()
                    ->scalarNode('port')->defaultValue(9200)->end()
                    ->scalarNode('path')->defaultValue('/')->end()
                    ->scalarNode('timeout')->defaultValue(5)->end()
                    ->scalarNode('index')->defaultValue('test')->end()
                    ->scalarNode('type')->defaultValue('test')->end()
                    ->end()
                ->end()
                ->arrayNode('selenium')
                    ->canBeUnset()
                    ->children()
                    ->scalarNode('server')->defaultValue('http://127.0.0.1:4444/wd/hub')->end()
                    ->scalarNode('domain')->defaultValue('http://127.0.0.1')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
