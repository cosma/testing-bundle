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

use Cosma\Bundle\TestingBundle\TestCase\ElasticTestCase;
use Elastica\Client;
use Elastica\Index;
use Elastica\Type;
use Symfony\Component\HttpKernel\HttpKernel;

class ElasticTestCaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see ElasticTestCase
     */
    public function testStaticAttributes()
    {
        $this->assertClassHasStaticAttribute('elasticClient', 'Cosma\Bundle\TestingBundle\TestCase\ElasticTestCase');
        $this->assertClassHasStaticAttribute('elasticIndex', 'Cosma\Bundle\TestingBundle\TestCase\ElasticTestCase');
        $this->assertClassHasStaticAttribute('elasticType', 'Cosma\Bundle\TestingBundle\TestCase\ElasticTestCase');
    }

    /**
     * @see ElasticTestCase::getElasticClient
     */
    public function testGetElasticClient()
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

    /**
     * @see ElasticTestCase::getElasticIndex
     */
    public function testGetElasticIndex()
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
        $container->expects($this->exactly(1))
            ->method('getParameter')
            ->will($this->returnValueMap($valueMap));

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getContainer'))
            ->getMockForAbstractClass();
        $kernel->expects($this->exactly(1))
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

        $reflectionMethod = $reflectionClass->getMethod('getElasticIndex');
        $reflectionMethod->setAccessible(true);

        /** @var Index $actual */
        $index = $reflectionMethod->invoke($elasticTestCase);

        $this->assertInstanceOf(
            'Elastica\Index',
            $index,
            'must return a Index object'
        );

        $reflectionMethod = $reflectionClass->getMethod('tearDownAfterClass');
        $reflectionMethod->invoke($elasticTestCase);

    }

    /**
     * @see ElasticTestCase::getElasticType
     */
    public function testGetElasticType()
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
        $container->expects($this->exactly(1))
            ->method('getParameter')
            ->will($this->returnValueMap($valueMap));

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getContainer'))
            ->getMockForAbstractClass();
        $kernel->expects($this->exactly(1))
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

        $reflectionMethod = $reflectionClass->getMethod('getElasticType');
        $reflectionMethod->setAccessible(true);

        /** @var Type $actual */
        $type = $reflectionMethod->invoke($elasticTestCase);

        $this->assertInstanceOf(
            'Elastica\Type',
            $type,
            'must return a Type object'
        );

        $reflectionMethod = $reflectionClass->getMethod('tearDownAfterClass');
        $reflectionMethod->invoke($elasticTestCase);

    }

    /**
     * @see ElasticTestCase::setUp
     */
    public function testSetUp()
    {
        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getContainer'))
            ->getMockForAbstractClass();

        $elasticIndex = $this->getMockBuilder('Elastica\Index')
            ->disableOriginalConstructor()
            ->setMethods(array('exists', 'create', 'delete'))
            ->getMock();
        $elasticIndex->expects($this->once())
            ->method('exists')
            ->will($this->returnValue(true));
        $elasticIndex->expects($this->once())
            ->method('delete')
            ->will($this->returnValue(true));
        $elasticIndex->expects($this->once())
            ->method('create')
            ->will($this->returnValue(true));

        $elasticTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\ElasticTestCase')
            ->disableOriginalConstructor()
            ->setMethods(array('getElasticIndex'))
            ->getMockForAbstractClass();
        $elasticTestCase->expects($this->exactly(3))
            ->method('getElasticIndex')
            ->will($this->returnValue($elasticIndex));


        $reflectionClassMocked = new \ReflectionClass($elasticTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $classProperty = $reflectionClass->getProperty('class');
        $classProperty->setAccessible(true);
        $classProperty->setValue($elasticTestCase, 'Cosma\Bundle\TestingBundle\Tests\AppKernel');

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernelProperty->setValue($elasticTestCase, $kernel);
        $setUpMethod = $reflectionClass->getMethod('setUp');
        $setUpMethod->setAccessible(true);
        $setUpMethod->invoke($elasticTestCase);

        $reflectionMethod = $reflectionClass->getMethod('tearDownAfterClass');
        $reflectionMethod->invoke($elasticTestCase);
    }
}

class ElasticTestCaseExample extends ElasticTestCase
{}




