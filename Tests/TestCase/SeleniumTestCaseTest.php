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
     * @see \Cosma\Bundle\TestingBundle\TestCase\SeleniumTestCase::setUp
     */
    public function testSetUp()
    {
        $testCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\SeleniumTestCase')
                         ->disableOriginalConstructor()
                         ->setMethods(['getRemoteWebDriver'])
                         ->getMockForAbstractClass()
        ;

        $testCase->expects($this->once())->method('getRemoteWebDriver');

        $reflectionClass = new \ReflectionClass($testCase);

        $classProperty = $reflectionClass->getParentClass()->getProperty('class');
        $classProperty->setAccessible(true);
        $classProperty->setValue($testCase, 'Cosma\Bundle\TestingBundle\Tests\AppKernel');

        $method = $reflectionClass->getParentClass()->getMethod('setUp');
        $method->setAccessible(true);
        $method->invoke($testCase);

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernel = $kernelProperty->getValue();

        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\AppKernel', $kernel, 'set up is wrong');

    }


    /**
     * @see \Cosma\Bundle\TestingBundle\TestCase\SeleniumTestCase::tearDown
     */
    public function testTearDown()
    {
        $testCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\SeleniumTestCase')
                         ->disableOriginalConstructor()
                         ->getMockForAbstractClass()
        ;

        $remoteWebDriver = $this->getMockBuilder('\Facebook\WebDriver\Remote\RemoteWebDriver')
                                ->disableOriginalConstructor()
                                ->setMethods(['close'])
                                ->getMock()
        ;
        $remoteWebDriver->expects($this->once())
                        ->method('close')
                        ->will($this->returnSelf())
        ;

        $reflectionClass = new \ReflectionClass($testCase);

        $property = $reflectionClass->getParentClass()->getProperty('remoteWebDriver');
        $property->setAccessible(true);
        $property->setValue($testCase, $remoteWebDriver);

        $classProperty = $reflectionClass->getParentClass()->getProperty('class');
        $classProperty->setAccessible(true);
        $classProperty->setValue($testCase, 'Cosma\Bundle\TestingBundle\Tests\AppKernel');

        $method = $reflectionClass->getParentClass()->getMethod('tearDown');
        $method->setAccessible(true);
        $method->invoke($testCase);

        $this->assertNull($property->getValue($testCase));
    }
}