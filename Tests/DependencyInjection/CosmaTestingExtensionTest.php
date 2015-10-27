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
        $config = array(
            'fixture_path' => 'Some/Fixture/Directory',
            'doctrine' => array(
                'cleaning_strategy' => 'drop'
            ),
            'solarium' => array(
                'host' => '127.0.0.1',
                'port' => 8080,
                'path' => '/solr',
                'core' => 'tests',
                'timeout' => 45
            ),
            'elastica' => array(
                'host' => '127.0.0.1',
                'port' => 9200,
                'path' => '/',
                'timeout' => 15,
                'index' => 'tests',
                'type' => 'tests'
            ),
            'selenium' => array(
                'domain' => '127.0.0.1:4444'
            )
        );

        $container = $this->getContainerWithLoadedExtension($config);

        $parameters = array(
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
            'cosma_testing.elastica.type',
            'cosma_testing.selenium.server',
            'cosma_testing.selenium.domain'
        );

        foreach ($parameters as $parameter) {
            $this->assertTrue($container->hasParameter($parameter), "Container doesn't has the parameter {$parameter}");
        }
    }

    /**
     * @see CosmaTestingExtension::load
     */
    public function testLoad_ConfigParameters_DefaultCleaning()
    {
        $config = array(
            'fixture_path' => 'Some/Fixture/Directory',
            'solarium' => array(
                'host' => '127.0.0.1',
                'port' => 8080,
                'path' => '/solr',
                'core' => 'tests',
                'timeout' => 45
            ),
            'elastica' => array(
                'host' => '127.0.0.1',
                'port' => 9200,
                'path' => '/',
                'timeout' => 15,
                'index' => 'tests',
                'type' => 'tests'
            ),
            'selenium' => array(
                'domain' => '127.0.0.1:4444'
            )
        );

        $container = $this->getContainerWithLoadedExtension($config);

        $parameters = array(
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
            'cosma_testing.elastica.type',
            'cosma_testing.selenium.server',
            'cosma_testing.selenium.domain'
        );

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
        $config = array(
            'fixture_path' => 'Some/Fixture/Directory',
            'doctrine'     => array(
                'cleaning_strategy' => 'qwerty'
            ),
            'solarium'     => array(
                'host'    => '127.0.0.1',
                'port'    => 8080,
                'path'    => '/solr',
                'core'    => 'tests',
                'timeout' => 45
            ),
            'elastica'     => array(
                'host'    => '127.0.0.1',
                'port'    => 9200,
                'path'    => '/',
                'timeout' => 15,
                'index'   => 'tests',
                'type'    => 'tests'
            ),
            'selenium'     => array(
                'domain' => '127.0.0.1:4444'
            )
        );

        $this->getContainerWithLoadedExtension($config);
    }

    /**
     * @see CosmaTestingExtension::getAlias
     */
    public function testGetAlias()
    {
        $container = new ContainerBuilder();

        $extension = new CosmaTestingExtension();

        $this->assertEquals('cosma_testing', $extension->getAlias(), 'Bundle ALias is wrong');
    }

    protected function getContainerWithLoadedExtension(array $config = array())
    {
        $container = new ContainerBuilder();

        $extension = new CosmaTestingExtension();
        $extension->load(array($config), $container);

        return $container;
    }
}
