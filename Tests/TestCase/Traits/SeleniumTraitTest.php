<?php
/**
 * This file is part of the TestingBundle project.
 *
 * @project    TestingBundle
 * @author     Cosmin Voicu <cosmin.voicu@oconotech.com>
 * @copyright  2015 - ocono Tech GmbH
 * @license    http://www.ocono-tech.com proprietary
 * @link       http://www.ocono-tech.com
 * @date       29/12/15
 */

namespace TestCase\Traits;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Symfony\Component\DependencyInjection\Container;

class SeleniumTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see                      \Cosma\Bundle\TestingBundle\TestCase\Traits\SeleniumTrait::getRemoteWebDriver
     *
     * @expectedException \Facebook\WebDriver\Exception\WebDriverCurlException
     * @expectedExceptionMessage Couldn't resolve host 'selenium.ro'
     */
    public function testGetRemoteWebDriver()
    {
        $container = new Container();

        $container->setParameter('cosma_testing.selenium.remote_server_url', 'http://selenium.ro/wd/hub');
        $container->setParameter('cosma_testing.selenium.test_domain', 'google.ro');

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\HttpKernelInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getContainer'])
                       ->getMockForAbstractClass()
        ;
        $kernel->expects($this->once())
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;

        $testCaseTrait = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\TestCase\Traits\SeleniumTrait')
                              ->disableOriginalConstructor()
                              ->setMethods(['getKernel'])
                              ->getMockForTrait()
        ;
        $testCaseTrait->expects($this->once())
                      ->method('getKernel')
                      ->will($this->returnValue($kernel))
        ;

        $reflectionClass = new \ReflectionClass($testCaseTrait);

        $reflectionMethod = $reflectionClass->getMethod('getRemoteWebDriver');
        $reflectionMethod->setAccessible(true);

        /** @type RemoteWebDriver $remoteWebDriver */
        $reflectionMethod->invoke($testCaseTrait);

        return [$testCaseTrait, $reflectionClass, $kernel];
    }

    /**
     * @see \Cosma\Bundle\TestingBundle\TestCase\Traits\SeleniumTrait::getTestDomain
     */
    public function testGetTestDomain()
    {
        $container = new Container();

        $container->setParameter('cosma_testing.selenium.remote_server_url', 'http://selenium.ro/wd/hub');
        $container->setParameter('cosma_testing.selenium.test_domain', 'google.ro');

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\HttpKernelInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getContainer'])
                       ->getMockForAbstractClass()
        ;
        $kernel->expects($this->once())
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;

        $testCaseTrait = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\TestCase\Traits\SeleniumTrait')
                              ->disableOriginalConstructor()
                              ->setMethods(['getKernel'])
                              ->getMockForTrait()
        ;
        $testCaseTrait->expects($this->once())
                      ->method('getKernel')
                      ->will($this->returnValue($kernel))
        ;

        $reflectionClass = new \ReflectionClass($testCaseTrait);

        $method = $reflectionClass->getMethod('getTestDomain');
        $method->setAccessible(true);
        $testDomain = $method->invoke($testCaseTrait);

        $this->assertEquals('google.ro', $testDomain);
    }

    /**
     * @see \Cosma\Bundle\TestingBundle\TestCase\Traits\SeleniumTrait::open
     */
    public function testOpen()
    {
        $container = new Container();

        $container->setParameter('cosma_testing.selenium.remote_server_url', 'http://selenium.ro/wd/hub');
        $container->setParameter('cosma_testing.selenium.test_domain', 'google.ro');

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\HttpKernelInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getContainer'])
                       ->getMockForAbstractClass()
        ;
        $kernel->expects($this->once())
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;

        $testCaseTrait = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\TestCase\Traits\SeleniumTrait')
                              ->disableOriginalConstructor()
                              ->setMethods(['getKernel'])
                              ->getMockForTrait()
        ;
        $testCaseTrait->expects($this->once())
                      ->method('getKernel')
                      ->will($this->returnValue($kernel))
        ;

        $remoteWebDriver = $this->getMockBuilder('\Facebook\WebDriver\Remote\RemoteWebDriver')
                                ->disableOriginalConstructor()
                                ->setMethods(['get'])
                                ->getMock()
        ;
        $remoteWebDriver->expects($this->once())
                        ->method('get')
                        ->with('http://google.ro/some_page.html')
                        ->will($this->returnSelf())
        ;

        $reflectionClass = new \ReflectionClass($testCaseTrait);

        $property = $reflectionClass->getParentClass()->getProperty('remoteWebDriver');
        $property->setAccessible(true);
        $property->setValue($testCaseTrait, $remoteWebDriver);

        $method = $reflectionClass->getMethod('open');
        $method->setAccessible(true);
        $method->invoke($testCaseTrait, '/some_page.html');
    }

    /**
     * @see \Cosma\Bundle\TestingBundle\TestCase\Traits\SeleniumTrait::openSecure
     */
    public function testOpenSecure()
    {
        $container = new Container();

        $container->setParameter('cosma_testing.selenium.remote_server_url', 'http://selenium.ro/wd/hub');
        $container->setParameter('cosma_testing.selenium.test_domain', 'google.ro');

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\HttpKernelInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getContainer'])
                       ->getMockForAbstractClass()
        ;
        $kernel->expects($this->once())
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;

        $testCaseTrait = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\TestCase\Traits\SeleniumTrait')
                              ->disableOriginalConstructor()
                              ->setMethods(['getKernel'])
                              ->getMockForTrait()
        ;
        $testCaseTrait->expects($this->once())
                      ->method('getKernel')
                      ->will($this->returnValue($kernel))
        ;

        $remoteWebDriver = $this->getMockBuilder('\Facebook\WebDriver\Remote\RemoteWebDriver')
                                ->disableOriginalConstructor()
                                ->setMethods(['get'])
                                ->getMock()
        ;
        $remoteWebDriver->expects($this->once())
                        ->method('get')
                        ->with('https://google.ro/secure_page.html')
                        ->will($this->returnSelf())
        ;

        $reflectionClass = new \ReflectionClass($testCaseTrait);

        $property = $reflectionClass->getParentClass()->getProperty('remoteWebDriver');
        $property->setAccessible(true);
        $property->setValue($testCaseTrait, $remoteWebDriver);

        $method = $reflectionClass->getMethod('openSecure');
        $method->setAccessible(true);
        $method->invoke($testCaseTrait, '/secure_page.html');
    }
}