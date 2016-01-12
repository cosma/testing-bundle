<?php
/**
 * This file is part of the "cosma/testing-bundle" project
 *
 * (c) Cosmin Voicu<cosmin.voicu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 11/01/15
 * Time: 23:33
 */
namespace Cosma\Bundle\TestingBundle\Tests\TestCase;

class RedisTestCaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see \Cosma\Bundle\TestingBundle\TestCase\RedisTestCase::setUp
     */
    public function testSetUp()
    {
        $testCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\RedisTestCase')
                         ->disableOriginalConstructor()
                         ->setMethods(['resetRedisDatabase'])
                         ->getMockForAbstractClass()
        ;

        $testCase->expects($this->once())->method('resetRedisDatabase');

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
    
}