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

namespace Cosma\Bundle\TestingBundle\Tests\TestCase;

use Cosma\Bundle\TestingBundle\TestCase\SeleniumTestCase;
use Elastica\Client;

class SeleniumTestCaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see SolrTestCase
     */
    public function testStaticAttributes()
    {
        $this->assertClassHasStaticAttribute('webDriver', 'Cosma\Bundle\TestingBundle\TestCase\SeleniumTestCase');
    }

    /**
     * @see SeleniumTestCase::setUp
     */
    public function testSetUp()
    {
        $webDriver = $this->getMockBuilder('\RemoteWebDriver')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getParameter'))
            ->getMockForAbstractClass();

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getContainer'))
            ->getMockForAbstractClass();
        $kernel->expects($this->exactly(5))
            ->method('getContainer')
            ->will($this->returnValue($container));

        $seleniumTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\SeleniumTestCase')
            ->disableOriginalConstructor()
            ->setMethods(array('getWebDriver'))
            ->getMockForAbstractClass();
        $seleniumTestCase->expects($this->once())
            ->method('getWebDriver')
            ->will($this->returnValue($webDriver));

        $reflectionClassMocked = new \ReflectionClass($seleniumTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();


        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernelProperty->setValue($seleniumTestCase, $kernel);

        $classProperty = $reflectionClass->getParentClass()->getProperty('class');
        $classProperty->setAccessible(true);
        $classProperty->setValue($seleniumTestCase, 'Cosma\Bundle\TestingBundle\Tests\AppKernel');


        $webDriverProperty = $reflectionClass->getProperty('webDriver');
        $webDriverProperty->setAccessible(TRUE);
        $webDriverProperty->setValue($seleniumTestCase, $webDriver);

        $setUpMethod = $reflectionClass->getMethod('setUp');
        $setUpMethod->setAccessible(true);
        $setUpMethod->invoke($seleniumTestCase);

        $reflectionMethod = $reflectionClass->getMethod('tearDown');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($seleniumTestCase);
    }

    /**
     * @see SeleniumTestCase::getWebDriver
     */
    public function testGetWebDriver()
    {
        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $seleniumTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\SeleniumTestCase')
            ->disableAutoload()
            ->getMockForAbstractClass();

        $reflectionClassMocked = new \ReflectionClass($seleniumTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $clientProperty = $reflectionClass->getProperty('kernel');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($seleniumTestCase, $kernel);

        $reflectionMethod = $reflectionClass->getMethod('getWebDriver');
        $reflectionMethod->setAccessible(true);

        /** @var \WebDriver $webDriver */
        $webDriver = $reflectionMethod->invoke($seleniumTestCase);

        $this->assertInstanceOf(
            '\WebDriver',
            $webDriver,
            'must return a \WebDriver object'
        );

        $reflectionMethod = $reflectionClass->getMethod('tearDown');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($seleniumTestCase);

    }
}

class SeleniumTestCaseExample extends SeleniumTestCase
{}