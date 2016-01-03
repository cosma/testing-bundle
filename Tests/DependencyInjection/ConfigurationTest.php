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

namespace Cosma\Bundle\TestingBundle\Tests\DependencyInjection;

use Cosma\Bundle\TestingBundle\DependencyInjection\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see Configuration::getConfigTreeBuilder
     */
    public function testGetConfigTreeBuilder()
    {
        $config = new Configuration();
        $tree   = $config->getConfigTreeBuilder();

        $node = $tree->buildTree();
        $this->assertEquals('cosma_testing', $node->getName());

        /** @var \Symfony\Component\Config\Definition\ScalarNode[] $options */
        $options = $node->getChildren();
        $this->assertCount(6, $options);
        $this->assertEquals('Fixture', $options['fixture_directory']->getDefaultValue());
        $this->assertEquals('Tests', $options['tests_directory']->getDefaultValue());

        /** @var \Symfony\Component\Config\Definition\ScalarNode[] $doctrineOptions */
        $doctrineOptions = $options['doctrine']->getChildren();
        $this->assertCount(1, $doctrineOptions);
        $this->assertEquals('truncate', $doctrineOptions['cleaning_strategy']->getDefaultValue());

        /** @var \Symfony\Component\Config\Definition\ScalarNode[] $solariumOptions */
        $solariumOptions = $options['solarium']->getChildren();
        $this->assertCount(5, $solariumOptions);
        $this->assertEquals('127.0.0.1', $solariumOptions['host']->getDefaultValue());
        $this->assertEquals('8080', $solariumOptions['port']->getDefaultValue());
        $this->assertEquals('/solr', $solariumOptions['path']->getDefaultValue());
        $this->assertEquals('test', $solariumOptions['core']->getDefaultValue());
        $this->assertEquals('5', $solariumOptions['timeout']->getDefaultValue());

        /** @var \Symfony\Component\Config\Definition\ScalarNode[] $elasticaOptions */
        $elasticaOptions = $options['elastica']->getChildren();
        $this->assertCount(6, $elasticaOptions);
        $this->assertEquals('127.0.0.1', $elasticaOptions['host']->getDefaultValue());
        $this->assertEquals('9200', $elasticaOptions['port']->getDefaultValue());
        $this->assertEquals('/', $elasticaOptions['path']->getDefaultValue());
        $this->assertEquals('5', $elasticaOptions['timeout']->getDefaultValue());
        $this->assertEquals('test', $elasticaOptions['index']->getDefaultValue());
        $this->assertEquals('test', $elasticaOptions['type']->getDefaultValue());

        /** @var \Symfony\Component\Config\Definition\ScalarNode[] $seleniumOptions */
        $seleniumOptions = $options['selenium']->getChildren();
        $this->assertCount(2, $seleniumOptions);
        $this->assertEquals('http://127.0.0.1:4444/wd/hub', $seleniumOptions['remote_server_url']->getDefaultValue());
        $this->assertEquals('localhost', $seleniumOptions['test_domain']->getDefaultValue());
    }
}
