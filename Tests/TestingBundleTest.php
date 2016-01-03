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

namespace Cosma\Bundle\TestingBundle\Tests;

use Cosma\Bundle\TestingBundle\TestingBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TestingBundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see TestingBundle::getContainerExtension
     */
    public function testGetContainerExtension()
    {
        $bundle = new TestingBundle();

        $this->assertInstanceOf(
            'Cosma\Bundle\TestingBundle\DependencyInjection\CosmaTestingExtension',
            $bundle->getContainerExtension()
        );
    }

    /**
     * @see TestingBundle::build
     */
    public function testBuild()
    {
        $containerBuilder = new ContainerBuilder();

        $bundle = new TestingBundle();

        $bundle->build($containerBuilder);

        $this->assertInstanceOf(
            'Cosma\Bundle\TestingBundle\DependencyInjection\Compiler\DoctrineMigrationsPass',
            $containerBuilder->getCompilerPassConfig()->getPasses()[1]
        );
    }
}