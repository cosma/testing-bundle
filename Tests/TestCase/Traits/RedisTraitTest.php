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

use Predis\Client;
use Symfony\Component\DependencyInjection\Container;

class RediTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see \Cosma\Bundle\TestingBundle\TestCase\Traits\RedisTrait::getRedisClient
     */
    public function testGetRedisClient()
    {
        $container = new Container();

        $container->setParameter('cosma_testing.redis.scheme', 'tcp');
        $container->setParameter('cosma_testing.redis.host', '127.0.0.1');
        $container->setParameter('cosma_testing.redis.port', 6379);
        $container->setParameter('cosma_testing.redis.database', 13);
        $container->setParameter('cosma_testing.redis.timeout', 5.0);

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\HttpKernelInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getContainer'])
                       ->getMockForAbstractClass()
        ;
        $kernel->expects($this->any())
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;

        $testCaseTrait = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\TestCase\Traits\RedisTrait')
                          ->disableOriginalConstructor()
                          ->setMethods(['getKernel'])
                          ->getMockForTrait()
        ;
        $testCaseTrait->expects($this->any())
                  ->method('getKernel')
                  ->will($this->returnValue($kernel))
        ;

        $reflectionClass = new \ReflectionClass($testCaseTrait);

        $reflectionMethod = $reflectionClass->getMethod('getRedisClient');
        $reflectionMethod->setAccessible(true);

        /** @type Client $redisClient */
        $redisClient = $reflectionMethod->invoke($testCaseTrait);

        $this->assertInstanceOf('\Predis\Client', $redisClient);

        return [ $testCaseTrait, $reflectionClass, $kernel, $container];
    }

    /**
     * @see \Cosma\Bundle\TestingBundle\TestCase\Traits\RedisTrait::resetRedisDatabase
     *
     * @depends testGetRedisClient
     */
    public function testResetRedisDatabase(array $options)
    {
        /** @type \ReflectionClass $reflectionClass */
        list($testCaseTrait, $reflectionClass, $kernel, $container) = $options;

        $client = $this->getMockBuilder('\Predis\Client')
                               ->disableOriginalConstructor()
                               ->setMethods(['select', 'flushdb'])
                               ->getMockForAbstractClass()
        ;
        $client->expects($this->once())
                       ->method('select')
                       ->will($this->returnValue(true))
        ;
        $client->expects($this->once())
                       ->method('flushdb')
                       ->will($this->returnValue(true))
        ;


        $kernel->expects($this->any())
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;

        $testCaseTrait->expects($this->any())
                      ->method('getKernel')
                      ->will($this->returnValue($kernel))
        ;

        $property = $reflectionClass->getParentClass()->getProperty('redisClient');
        $property->setAccessible(true);
        $property->setValue($testCaseTrait, $client);

        $method = $reflectionClass->getMethod('resetRedisDatabase');
        $method->setAccessible(true);
        $method->invoke($testCaseTrait);
    }
}