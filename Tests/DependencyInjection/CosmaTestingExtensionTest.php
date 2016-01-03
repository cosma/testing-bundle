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

use Cosma\Bundle\TestingBundle\DependencyInjection\CosmaTestingExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;

class CosmaTestingExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see CosmaTestingExtension::load
     */
    public function testLoad_ConfigParameters()
    {
        $config = [
            'fixture_directory' => 'Some/Fixture/Directory',
            'doctrine'          => [
                'cleaning_strategy' => 'drop'
            ],
            'solarium'          => [
                'host'    => '127.0.0.1',
                'port'    => 8080,
                'path'    => '/solr',
                'core'    => 'tests',
                'timeout' => 45
            ],
            'elastica'          => [
                'host'    => '127.0.0.1',
                'port'    => 9200,
                'path'    => '/',
                'timeout' => 15,
                'index'   => 'tests'
            ],
            'selenium'          => [
                'remote_server_url' => '127.0.0.1:4444',
                'test_domain' => 'localhost'
            ]
        ];

        $container = $this->getContainerWithLoadedExtension($config);

        $parameters = [
            'cosma_testing.fixture_directory',
            'cosma_testing.tests_directory',
            'cosma_testing.doctrine.cleaning_strategy',
            'cosma_testing.solarium.host',
            'cosma_testing.solarium.port',
            'cosma_testing.solarium.path',
            'cosma_testing.solarium.core',
            'cosma_testing.solarium.timeout',
            'cosma_testing.elastica.host',
            'cosma_testing.elastica.port',
            'cosma_testing.elastica.path',
            'cosma_testing.elastica.timeout',
            'cosma_testing.elastica.index',
            'cosma_testing.selenium.remote_server_url',
            'cosma_testing.selenium.test_domain'
        ];

        foreach ($parameters as $parameter) {
            $this->assertTrue($container->hasParameter($parameter), "Container doesn't has the parameter {$parameter}");
        }
    }

    /**
     * @see CosmaTestingExtension::load
     */
    public function testLoad_ConfigParameters_DefaultCleaning()
    {
        $config = [
            'fixture_directory' => 'Some/Fixture/Directory',
            'solarium'          => [
                'host'    => '127.0.0.1',
                'port'    => 8080,
                'path'    => '/solr',
                'core'    => 'tests',
                'timeout' => 45
            ],
            'elastica'          => [
                'host'    => '127.0.0.1',
                'port'    => 9200,
                'path'    => '/',
                'timeout' => 15,
                'index'   => 'tests'
            ],
            'selenium'          => [
                'remote_server_url' => '127.0.0.1:4444',
                'test_domain' => 'localhost'
            ]
        ];

        $container = $this->getContainerWithLoadedExtension($config);

        $parameters = [
            'cosma_testing.fixture_directory',
            'cosma_testing.tests_directory',
            'cosma_testing.doctrine.cleaning_strategy',
            'cosma_testing.solarium.host',
            'cosma_testing.solarium.port',
            'cosma_testing.solarium.path',
            'cosma_testing.solarium.core',
            'cosma_testing.solarium.timeout',
            'cosma_testing.elastica.host',
            'cosma_testing.elastica.port',
            'cosma_testing.elastica.path',
            'cosma_testing.elastica.timeout',
            'cosma_testing.elastica.index',
            'cosma_testing.selenium.remote_server_url',
            'cosma_testing.selenium.test_domain'
        ];

        foreach ($parameters as $parameter) {
            $this->assertTrue($container->hasParameter($parameter), "Container doesn't has the parameter {$parameter}");
        }
    }

    /**
     * @see CosmaTestingExtension::load
     *
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testLoad_ConfigParameters_Exception()
    {
        $config = [
            'fixture_directory' => 'Some/Fixture/Directory',
            'doctrine'          => [
                'cleaning_strategy' => 'qwerty'
            ],
            'solarium'          => [
                'host'    => '127.0.0.1',
                'port'    => 8080,
                'path'    => '/solr',
                'core'    => 'tests',
                'timeout' => 45
            ],
            'elastica'          => [
                'host'    => '127.0.0.1',
                'port'    => 9200,
                'path'    => '/',
                'timeout' => 15,
                'index'   => 'tests'
            ],
            'selenium'          => [
                'remote_server_url' => '127.0.0.1:4444',
                'test_domain' => 'localhost'
            ]
        ];

        $this->getContainerWithLoadedExtension($config);
    }

    /**
     * @see CosmaTestingExtension::getAlias
     */
    public function testGetAlias()
    {
        $container = new ContainerBuilder();

        $extension = new CosmaTestingExtension();

        $this->assertEquals('cosma_testing', $extension->getAlias(), 'Bundle Alias is wrong');
    }

    protected function getContainerWithLoadedExtension(array $config = [])
    {
        $container = new ContainerBuilder();

        $extension = new CosmaTestingExtension();
        $extension->load([$config], $container);

        return $container;
    }
}
