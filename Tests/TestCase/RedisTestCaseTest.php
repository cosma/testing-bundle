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

use Cosma\Bundle\TestingBundle\TestCase\RedisTestCase;
use Predis\Client;
use Symfony\Component\DependencyInjection\Container;

class RedisTestCaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see RedisTestCase
     */
    public function testStaticAttributes()
    {
        $this->assertClassHasStaticAttribute('redisClient', 'Cosma\Bundle\TestingBundle\TestCase\RedisTestCase');
    }

    /**
     * @see RedisTestCase::setUp
     */
    public function testSetUp()
    {
        $redisTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\RedisTestCase')
                              ->disableOriginalConstructor()
                              ->setMethods(['getRedisClient', 'resetRedisDatabase'])
                              ->getMockForAbstractClass()
        ;

        $redisTestCase->expects($this->once())
                      ->method('resetRedisDatabase')
                      ->will($this->returnValue(null))
        ;

        $reflectionClass = new \ReflectionClass($redisTestCase);

        $classProperty = $reflectionClass->getParentClass()->getProperty('class');
        $classProperty->setAccessible(true);
        $classProperty->setValue($redisTestCase, 'Cosma\Bundle\TestingBundle\Tests\AppKernel');

        $resetRedisCoreMethod = $reflectionClass->getParentClass()->getMethod('resetRedisDatabase');
        $resetRedisCoreMethod->setAccessible(true);

        $setUpMethod = $reflectionClass->getParentClass()->getMethod('setUp');
        $setUpMethod->setAccessible(true);
        $setUpMethod->invoke($redisTestCase);

        $reflectionMethod = $reflectionClass->getParentClass()->getMethod('tearDownAfterClass');
        $reflectionMethod->invoke($redisTestCase);
    }

    /**
     * @see RedisTestCase::getRedisClient
     */
    public function testGetRedisClient()
    {
        $valueMap = [
            ['cosma_testing.redis.scheme', 'tcp'],
            ['cosma_testing.redis.host', '127.0.0.1'],
            ['cosma_testing.redis.port', '6379'],
            ['cosma_testing.redis.timeout', '5']
        ];

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
                          ->disableOriginalConstructor()
                          ->setMethods(['getParameter'])
                          ->getMockForAbstractClass()
        ;
        $container->expects($this->exactly(4))
                  ->method('getParameter')
                  ->will($this->returnValueMap($valueMap))
        ;

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getContainer'])
                       ->getMockForAbstractClass()
        ;
        $kernel->expects($this->exactly(4))
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;

        $redisTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\RedisTestCase')
                              ->disableAutoload()
                              ->getMockForAbstractClass()
        ;

        $reflectionClassMocked = new \ReflectionClass($redisTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $clientProperty = $reflectionClass->getProperty('kernel');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($redisTestCase, $kernel);

        $reflectionMethod = $reflectionClass->getMethod('getRedisClient');
        $reflectionMethod->setAccessible(true);

        /** @var Client $client */
        $client = $reflectionMethod->invoke($redisTestCase);

        $this->assertInstanceOf(
            'Predis\Client',
            $client,
            'must return a Client object'
        );

        $reflectionMethod = $reflectionClass->getMethod('tearDownAfterClass');
        $reflectionMethod->invoke($redisTestCase);
    }
}

class RedisTestCaseExample extends RedisTestCase
{
}




