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
        $this->assertClassHasAttribute('webDriver', 'Cosma\Bundle\TestingBundle\TestCase\SeleniumTestCase');
    }

    /**
     * @see SeleniumTestCase::setUp
     */
    public function testSetUp()
    {
        $webDriver = $this->getMockBuilder('\RemoteWebDriver')
            ->disableOriginalConstructor()
            ->setMethods(array('execute'))
            ->getMockForAbstractClass();

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

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
        $kernelProperty->setAccessible(TRUE);
        $kernelProperty->setValue($seleniumTestCase, $kernel);

        $classProperty = $reflectionClass->getParentClass()->getProperty('class');
        $classProperty->setAccessible(TRUE);
        $classProperty->setValue($seleniumTestCase, 'Cosma\Bundle\TestingBundle\Tests\AppKernel');


        $webDriverProperty = $reflectionClass->getProperty('webDriver');
        $webDriverProperty->setAccessible(TRUE);
        $webDriverProperty->setValue($seleniumTestCase, $webDriver);

        $setUpMethod = $reflectionClass->getMethod('setUp');
        $setUpMethod->setAccessible(TRUE);
        $setUpMethod->invoke($seleniumTestCase);
    }

    /**
     * @see SeleniumTestCase::getWebDriver
     *
     * @expectedException \WebDriverCurlException
     */
    public function testGetWebDriver()
    {
        $valueMap = array(
            array('cosma_testing.selenium.server', 'http://127.0.0.1:4444/wd/hub')
        );

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getParameter'))
            ->getMockForAbstractClass();
        $container->expects($this->once())
            ->method('getParameter')
            ->will($this->returnValueMap($valueMap));

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $kernel->expects($this->once())
            ->method('getContainer')
            ->will($this->returnValue($container));

        $seleniumTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\SeleniumTestCase')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $reflectionClassMocked = new \ReflectionClass($seleniumTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $clientProperty = $reflectionClass->getProperty('kernel');
        $clientProperty->setAccessible(TRUE);
        $clientProperty->setValue($seleniumTestCase, $kernel);

        $reflectionMethod = $reflectionClass->getMethod('getWebDriver');
        $reflectionMethod->setAccessible(TRUE);

        $reflectionMethod->invoke($seleniumTestCase);
    }

    /**
     * @see SeleniumTestCase::open
     */
    public function testOpen()
    {
        $webDriver = $this->getMockBuilder('\RemoteWebDriver')
            ->disableOriginalConstructor()
            ->setMethods(array( 'execute', 'get'))
            ->getMockForAbstractClass();
        $webDriver->expects($this->once())
            ->method('get')
            ->with('http://wwww.google.ro/site.html')
            ->will($this->returnSelf());

        $valueMap = array(
            array('cosma_testing.selenium.domain', 'http://wwww.google.ro')
        );

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getParameter'))
            ->getMockForAbstractClass();
        $container->expects($this->once())
            ->method('getParameter')
            ->will($this->returnValueMap($valueMap));

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getContainer'))
            ->getMockForAbstractClass();
        $kernel->expects($this->once())
            ->method('getContainer')
            ->will($this->returnValue($container));

        /** @var SeleniumTestCase $seleniumTestCase */
        $seleniumTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\SeleniumTestCase')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $reflectionClassMocked = new \ReflectionClass($seleniumTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $clientProperty = $reflectionClass->getProperty('kernel');
        $clientProperty->setAccessible(TRUE);
        $clientProperty->setValue($seleniumTestCase, $kernel);

        $webDriverProperty = $reflectionClass->getProperty('webDriver');
        $webDriverProperty->setAccessible(TRUE);
        $webDriverProperty->setValue($seleniumTestCase, $webDriver);

        $seleniumTestCase->open('/site.html');
    }

    /**
     * @see SeleniumTestCase::getDomain
     */
    public function testGetDomain()
    {
        $webDriver = $this->getMockBuilder('\RemoteWebDriver')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $valueMap = array(
            array('cosma_testing.selenium.domain', 'http://wwww.google.ro')
        );

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getParameter'))
            ->getMockForAbstractClass();
        $container->expects($this->once())
            ->method('getParameter')
            ->will($this->returnValueMap($valueMap));

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getContainer'))
            ->getMockForAbstractClass();
        $kernel->expects($this->once())
            ->method('getContainer')
            ->will($this->returnValue($container));

        /** @var SeleniumTestCase $seleniumTestCase */
        $seleniumTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\SeleniumTestCase')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $reflectionClassMocked = new \ReflectionClass($seleniumTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $clientProperty = $reflectionClass->getProperty('kernel');
        $clientProperty->setAccessible(TRUE);
        $clientProperty->setValue($seleniumTestCase, $kernel);

        $webDriverProperty = $reflectionClass->getProperty('webDriver');
        $webDriverProperty->setAccessible(TRUE);
        $webDriverProperty->setValue($seleniumTestCase, $webDriver);

        $domain = $seleniumTestCase->getDomain();

        $this->assertEquals('http://wwww.google.ro', $domain, 'Domain is wong');
    }

}

class SeleniumTestCaseExample extends SeleniumTestCase
{}