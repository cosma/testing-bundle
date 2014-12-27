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


    public function testConfigParameters()
    {
        $config = array(
            'fixture_path' => 'Some/Fixture/Directory',
            'solarium' => array(
                'host' => '127.0.0.1',
                'port' => 8080,
                'path' => '/solr',
                'core' => 'tests',
                'timeout' => 45

            )
        );

        $container = $this->getContainerWithLoadedExtension($config);

        $parameters = array(
            'cosma_testing.fixture_path',
            'cosma_testing.fixture_table_directory',
            'cosma_testing.fixture_test_directory',
            'cosma_testing.solarium.host',
            'cosma_testing.solarium.port',
            'cosma_testing.solarium.path',
            'cosma_testing.solarium.core',
            'cosma_testing.solarium.timeout'
        );

        foreach ($parameters as $parameter) {
            $this->assertTrue($container->hasParameter($parameter), "Container doesn't has the parameter {$parameter}");
        }
    }

    protected function getContainerWithLoadedExtension(array $config = array())
    {
        $container = new ContainerBuilder();

        $extension = new CosmaTestingExtension();
        $extension->load(array($config), $container);

        return $container;
    }


}
