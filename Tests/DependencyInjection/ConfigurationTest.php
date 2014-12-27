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

/**
 * Class ConfigurationTest
 *
 * @author Julius Beckmann <github@h4cc.de>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetConfigTreeBuilder()
    {
        $config = new Configuration();
        $tree = $config->getConfigTreeBuilder();

        $node = $tree->buildTree();
        $this->assertEquals('cosma_testing', $node->getName());

        /** @var \Symfony\Component\Config\Definition\ScalarNode[] $options */
        $options = $node->getChildren();
        $this->assertCount(4, $options);
        $this->assertEquals('Fixture', $options['fixture_path']->getDefaultValue());
        $this->assertEquals('Table', $options['fixture_table_directory']->getDefaultValue());
        $this->assertEquals('Test', $options['fixture_test_directory']->getDefaultValue());

        $solariumOptions = $options['solarium']->getChildren();
        $this->assertCount(5, $solariumOptions);
        $this->assertEquals('127.0.0.1', $solariumOptions['host']->getDefaultValue());
        $this->assertEquals('8080', $solariumOptions['port']->getDefaultValue());
        $this->assertEquals('/solr', $solariumOptions['path']->getDefaultValue());
        $this->assertEquals('test', $solariumOptions['core']->getDefaultValue());
        $this->assertEquals('5', $solariumOptions['timeout']->getDefaultValue());
    }
}
