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
use Facebook\WebDriver\Exception\WebDriverCurlException;

class SeleniumTestCaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see SeleniumTestCase::setUp
     */
    public function testSetUp()
    {
        $webDriver = $this->getMockBuilder('Facebook\WebDriver\Remote\RemoteWebDriver')
                          ->disableOriginalConstructor()
                          ->setMethods(['execute'])
                          ->getMockForAbstractClass()
        ;

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
                       ->disableOriginalConstructor()
                       ->getMockForAbstractClass()
        ;

        $seleniumTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\SeleniumTestCase')
                                 ->disableOriginalConstructor()
                                 ->setMethods(['getWebDriver'])
                                 ->getMockForAbstractClass()
        ;
        $seleniumTestCase->expects($this->once())
                         ->method('getWebDriver')
                         ->will($this->returnValue($webDriver))
        ;

        $reflectionClassMocked = new \ReflectionClass($seleniumTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernelProperty->setValue($seleniumTestCase, $kernel);

        $classProperty = $reflectionClass->getParentClass()->getProperty('class');
        $classProperty->setAccessible(true);
        $classProperty->setValue($seleniumTestCase, 'Cosma\Bundle\TestingBundle\Tests\AppKernel');

        $webDriverProperty = $reflectionClass->getProperty('webDriver');
        $webDriverProperty->setAccessible(true);
        $webDriverProperty->setValue($seleniumTestCase, $webDriver);

        $setUpMethod = $reflectionClass->getMethod('setUp');
        $setUpMethod->setAccessible(true);
        $setUpMethod->invoke($seleniumTestCase);
    }

    /**
     * @see SeleniumTestCase::tearDown
     */
    public function testTearDown()
    {
        $webDriver = $this->getMockBuilder('Facebook\WebDriver\Remote\RemoteWebDriver')
                          ->disableOriginalConstructor()
                          ->setMethods(['execute'])
                          ->getMockForAbstractClass()
        ;

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
                       ->disableOriginalConstructor()
                       ->getMockForAbstractClass()
        ;

        $seleniumTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\SeleniumTestCase')
                                 ->disableOriginalConstructor()
                                 ->getMockForAbstractClass()
        ;

        $reflectionClassMocked = new \ReflectionClass($seleniumTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernelProperty->setValue($seleniumTestCase, $kernel);

        $classProperty = $reflectionClass->getParentClass()->getProperty('class');
        $classProperty->setAccessible(true);
        $classProperty->setValue($seleniumTestCase, 'Cosma\Bundle\TestingBundle\Tests\AppKernel');

        $webDriverProperty = $reflectionClass->getProperty('webDriver');
        $webDriverProperty->setAccessible(true);
        $webDriverProperty->setValue($seleniumTestCase, $webDriver);

        $setUpMethod = $reflectionClass->getMethod('tearDown');
        $setUpMethod->setAccessible(true);
        $setUpMethod->invoke($seleniumTestCase);

        $afterProperty = $reflectionClass->getProperty('webDriver');
        $afterProperty->setAccessible(true);
        $this->assertNull($afterProperty->getValue($seleniumTestCase));
    }

    /**
     * @see SeleniumTestCase::getWebDriver
     */
    public function testGetWebDriver()
    {
        $webDriver = $this->getMockBuilder('Facebook\WebDriver\Remote\RemoteWebDriver')
                          ->disableOriginalConstructor()
                          ->getMock()
        ;

        $seleniumTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\SeleniumTestCase')
                                 ->disableOriginalConstructor()
                                 ->getMockForAbstractClass()
        ;

        $reflectionClassMocked = new \ReflectionClass($seleniumTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $webDriverProperty = $reflectionClass->getProperty('webDriver');
        $webDriverProperty->setAccessible(true);
        $webDriverProperty->setValue($seleniumTestCase, $webDriver);

        $reflectionMethod = $reflectionClass->getMethod('getWebDriver');
        $reflectionMethod->setAccessible(true);

        $webDriver = $reflectionMethod->invoke($seleniumTestCase);

        $this->assertInstanceOf('Facebook\WebDriver\Remote\RemoteWebDriver', $webDriver);
    }

    /**
     * @see SeleniumTestCase::getWebDriver
     *
     * @expectedException \Facebook\WebDriver\Exception\WebDriverCurlException
     */
    public function testGetWebDriver_NUll()
    {
        $valueMap = [
            ['cosma_testing.selenium.server', 'http://127.0.0.1:4444/wd/hub']
        ];

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
                          ->disableOriginalConstructor()
                          ->setMethods(['getParameter'])
                          ->getMockForAbstractClass()
        ;
        $container->expects($this->once())
                  ->method('getParameter')
                  ->will($this->returnValueMap($valueMap))
        ;

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
                       ->disableOriginalConstructor()
                       ->getMockForAbstractClass()
        ;
        $kernel->expects($this->once())
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;

        $seleniumTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\SeleniumTestCase')
                                 ->disableOriginalConstructor()
                                 ->getMockForAbstractClass()
        ;

        $reflectionClassMocked = new \ReflectionClass($seleniumTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $clientProperty = $reflectionClass->getProperty('kernel');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($seleniumTestCase, $kernel);

        $reflectionMethod = $reflectionClass->getMethod('getWebDriver');
        $reflectionMethod->setAccessible(true);

        $webDriver = $reflectionMethod->invoke($seleniumTestCase);

        $this->assertInstanceOf('Facebook\WebDriver\Remote\RemoteWebDriver', $webDriver);
    }

    /**
     * @see SeleniumTestCase::open
     */
    public function testOpen()
    {
        $webDriver = $this->getMockBuilder('Facebook\WebDriver\Remote\RemoteWebDriver')
                          ->disableOriginalConstructor()
                          ->setMethods(['execute', 'get'])
                          ->getMockForAbstractClass()
        ;
        $webDriver->expects($this->once())
                  ->method('get')
                  ->with('http://wwww.google.ro/site.html')
                  ->will($this->returnSelf())
        ;

        $valueMap = [
            ['cosma_testing.selenium.domain', 'http://wwww.google.ro']
        ];

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
                          ->disableOriginalConstructor()
                          ->setMethods(['getParameter'])
                          ->getMockForAbstractClass()
        ;
        $container->expects($this->once())
                  ->method('getParameter')
                  ->will($this->returnValueMap($valueMap))
        ;

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getContainer'])
                       ->getMockForAbstractClass()
        ;
        $kernel->expects($this->once())
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;

        /** @var SeleniumTestCase $seleniumTestCase */
        $seleniumTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\SeleniumTestCase')
                                 ->disableOriginalConstructor()
                                 ->getMockForAbstractClass()
        ;

        $reflectionClassMocked = new \ReflectionClass($seleniumTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $clientProperty = $reflectionClass->getProperty('kernel');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($seleniumTestCase, $kernel);

        $webDriverProperty = $reflectionClass->getProperty('webDriver');
        $webDriverProperty->setAccessible(true);
        $webDriverProperty->setValue($seleniumTestCase, $webDriver);

        $webDriver = $seleniumTestCase->open('/site.html');

        $this->assertInstanceOf(
            'Facebook\WebDriver\Remote\RemoteWebDriver',
            $webDriver,
            'must return a RemoteWebDriver object'
        );
    }

    /**
     * @see SeleniumTestCase::getDomain
     */
    public function testGetDomain()
    {
        $webDriver = $this->getMockBuilder('Facebook\WebDriver\Remote\RemoteWebDriver')
                          ->disableOriginalConstructor()
                          ->getMockForAbstractClass()
        ;

        $valueMap = [
            ['cosma_testing.selenium.domain', 'http://wwww.google.ro']
        ];

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
                          ->disableOriginalConstructor()
                          ->setMethods(['getParameter'])
                          ->getMockForAbstractClass()
        ;
        $container->expects($this->once())
                  ->method('getParameter')
                  ->will($this->returnValueMap($valueMap))
        ;

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getContainer'])
                       ->getMockForAbstractClass()
        ;
        $kernel->expects($this->once())
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;

        /** @var SeleniumTestCase $seleniumTestCase */
        $seleniumTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\SeleniumTestCase')
                                 ->disableOriginalConstructor()
                                 ->getMockForAbstractClass()
        ;

        $reflectionClassMocked = new \ReflectionClass($seleniumTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $clientProperty = $reflectionClass->getProperty('kernel');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($seleniumTestCase, $kernel);

        $webDriverProperty = $reflectionClass->getProperty('webDriver');
        $webDriverProperty->setAccessible(true);
        $webDriverProperty->setValue($seleniumTestCase, $webDriver);

        $domain = $seleniumTestCase->getDomain();

        $this->assertEquals('http://wwww.google.ro', $domain, 'Domain is wong');
    }
}