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

class SeleniumTestCaseTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @see SeleniumTestCase::setUp
     */
    public function testSetUp()
    {
        $valueMap = array(
            array('cosma_testing.selenium.server', 'http://127.0.0.1:4444/wd/hub'),
            array('cosma_testing.selenium.domain', 'http://127.0.0.1')
        );

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')
            ->disableOriginalConstructor()
            ->disableAutoload()
            ->setMethods(array('getParameter'))
            ->getMock();
        $container->expects($this->once())
            ->method('getParameter')
            ->will($this->returnValueMap($valueMap));

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getContainer'))
            ->getMockForAbstractClass();
        $kernel->expects($this->exactly(4))
            ->method('getContainer')
            ->will($this->returnValue($container));

        $seleniumTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\SeleniumTestCase')
            ->disableOriginalConstructor()
            ->disableAutoload()
            ->getMockForAbstractClass();


        $reflectionClassMocked = new \ReflectionClass($seleniumTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $classProperty = $reflectionClass->getProperty('class');
        $classProperty->setAccessible(true);
        $classProperty->setValue($seleniumTestCase, 'Cosma\Bundle\TestingBundle\Tests\AppKernel');

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernelProperty->setValue($seleniumTestCase, $kernel);

        $setUpMethod = $reflectionClass->getMethod('setUp');
        $setUpMethod->setAccessible(true);
        $setUpMethod->invoke($seleniumTestCase);

        $reflectionMethod = $reflectionClass->getMethod('tearDownAfterClass');
        $reflectionMethod->invoke($seleniumTestCase);
    }

    /**
     * @see SeleniumTestCase::getWebDriver
     */
    public function etestGetWebDriver()
    {
        $valueMap = array(
            array('cosma_testing.elastica.host', '127.0.0.1'),
            array('cosma_testing.elastica.port', '8080'),
            array('cosma_testing.elastica.path', '/'),
            array('cosma_testing.elastica.timeout', '5'),
            array('cosma_testing.elastica.index', 'test'),
            array('cosma_testing.elastica.type', 'test')
        );

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getParameter'))
            ->getMockForAbstractClass();
        $container->expects($this->exactly(4))
            ->method('getParameter')
            ->will($this->returnValueMap($valueMap));

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getContainer'))
            ->getMockForAbstractClass();
        $kernel->expects($this->exactly(4))
            ->method('getContainer')
            ->will($this->returnValue($container));

        $elasticTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\ElasticTestCase')
            ->disableAutoload()
            ->getMockForAbstractClass();

        $reflectionClassMocked = new \ReflectionClass($elasticTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $clientProperty = $reflectionClass->getProperty('kernel');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($elasticTestCase, $kernel);

        $reflectionMethod = $reflectionClass->getMethod('getElasticClient');
        $reflectionMethod->setAccessible(true);

        /** @var Client $actual */
        $client = $reflectionMethod->invoke($elasticTestCase);

        $this->assertInstanceOf(
            'Elastica\Client',
            $client,
            'must return a Client object'
        );

        $reflectionMethod = $reflectionClass->getMethod('tearDownAfterClass');
        $reflectionMethod->invoke($elasticTestCase);

    }


}

class SeleniumTestCaseExample extends SeleniumTestCase
{}




