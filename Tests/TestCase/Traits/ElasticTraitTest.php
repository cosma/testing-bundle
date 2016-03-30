<?php
/**
 * This file is part of the "cosma/testing-bundle" project
 *
 * (c) Cosmin Voicu<cosmin.voicu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 01/01/16
 * Time: 23:33
 */

namespace TestCase\Traits;

use Elastica\Client;
use Elastica\Index;
use Symfony\Component\DependencyInjection\Container;

class ElasticTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see \Cosma\Bundle\TestingBundle\TestCase\Traits\ElasticTrait::getElasticClient
     */
    public function testGetElasticClient()
    {
        $container = new Container();

        $container->setParameter('cosma_testing.elastica.host', '127.0.0.1');
        $container->setParameter('cosma_testing.elastica.port', 8080);
        $container->setParameter('cosma_testing.elastica.path', '/search');
        $container->setParameter('cosma_testing.elastica.timeout', 10);

        $container->setParameter('cosma_testing.elastica.index', 'test_index');

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\HttpKernelInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getContainer'])
                       ->getMockForAbstractClass()
        ;
        $kernel->expects($this->any())
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;

        $testCaseTrait = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\TestCase\Traits\ElasticTrait')
                              ->disableOriginalConstructor()
                              ->setMethods(['getKernel'])
                              ->getMockForTrait()
        ;
        $testCaseTrait->expects($this->any())
                      ->method('getKernel')
                      ->will($this->returnValue($kernel))
        ;

        $reflectionClass = new \ReflectionClass($testCaseTrait);

        $reflectionMethod = $reflectionClass->getMethod('getElasticClient');
        $reflectionMethod->setAccessible(true);
        /** @type Client $elasticClient */
        $elasticClient = $reflectionMethod->invoke($testCaseTrait);

        $this->assertInstanceOf('\Elastica\Client', $elasticClient);

        $this->assertEquals(
            [
                'host'               => '127.0.0.1',
                'port'               => 8080,
                'path'               => '/search',
                'timeout'            => 10,
                'url'                => null,
                'proxy'              => null,
                'transport'          => null,
                'persistent'         => true,
                'connections'        => [],
                'roundRobin'         => false,
                'log'                => false,
                'retryOnConflict'    => 0,
                'connectionStrategy' => 'Simple',
                'bigintConversion'   => false,
                'username'           => null,
                'password'           => null,
            ],
            $elasticClient->getConfig()
        );

        return [$testCaseTrait, $reflectionClass, $kernel, $container];
    }

    /**
     * @see     \Cosma\Bundle\TestingBundle\TestCase\Traits\ElasticTrait::getElasticIndex
     *
     * @depends testGetElasticClient
     */
    public function testGetElasticIndex(array $options)
    {
        /** @type \ReflectionClass $reflectionClass */
        list($testCaseTrait, $reflectionClass, $kernel, $container) = $options;

        $kernel->expects($this->any())
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;

        $testCaseTrait->expects($this->any())
                      ->method('getKernel')
                      ->will($this->returnValue($kernel))
        ;

        $reflectionMethod = $reflectionClass->getMethod('getElasticIndex');
        $reflectionMethod->setAccessible(true);
        /** @type Index $elasticIndex */
        $elasticIndex = $reflectionMethod->invoke($testCaseTrait);

        $this->assertInstanceOf('\Elastica\Index', $elasticIndex);

        $this->assertEquals('test_index', $elasticIndex->getName()
        );

        return [$testCaseTrait, $reflectionClass];
    }

    /**
     * @see     \Cosma\Bundle\TestingBundle\TestCase\Traits\ElasticTrait::recreateIndex
     *
     * @depends testGetElasticIndex
     */
    public function testRecreateIndex_WithDelete(array $options)
    {
        /** @type \ReflectionClass $reflectionClass */
        list($testCaseTrait, $reflectionClass) = $options;

        $elasticIndex = $this->getMockBuilder('\Elastica\Index')
                             ->disableOriginalConstructor()
                             ->setMethods(['exists', 'delete', 'create'])
                             ->getMockForAbstractClass()
        ;
        $elasticIndex->expects($this->once())
                     ->method('exists')
                     ->will($this->returnValue(true))
        ;
        $elasticIndex->expects($this->once())
                     ->method('delete')
        ;
        $elasticIndex->expects($this->once())
                     ->method('create')
        ;

        $property = $reflectionClass->getParentClass()->getProperty('elasticIndex');
        $property->setAccessible(true);
        $property->setValue($testCaseTrait, $elasticIndex);

        $method = $reflectionClass->getMethod('recreateIndex');
        $method->setAccessible(true);
        $method->invoke($testCaseTrait);
    }

    /**
     * @see     \Cosma\Bundle\TestingBundle\TestCase\Traits\ElasticTrait::recreateIndex
     *
     * @depends testGetElasticIndex
     */
    public function testRecreateIndex_WithoutDelete(array $options)
    {
        /** @type \ReflectionClass $reflectionClass */
        list($testCaseTrait, $reflectionClass) = $options;

        $elasticIndex = $this->getMockBuilder('\Elastica\Index')
                             ->disableOriginalConstructor()
                             ->setMethods(['exists', 'delete', 'create'])
                             ->getMockForAbstractClass()
        ;
        $elasticIndex->expects($this->once())
                     ->method('exists')
                     ->will($this->returnValue(false))
        ;
        $elasticIndex->expects($this->once())
                     ->method('create')
        ;

        $property = $reflectionClass->getParentClass()->getProperty('elasticIndex');
        $property->setAccessible(true);
        $property->setValue($testCaseTrait, $elasticIndex);

        $method = $reflectionClass->getMethod('recreateIndex');
        $method->setAccessible(true);
        $method->invoke($testCaseTrait);
    }
}