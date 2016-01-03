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

use Cosma\Bundle\TestingBundle\Tests\AnotherExampleEntity;
use Cosma\Bundle\TestingBundle\Tests\SomeEntity;
use Doctrine\DBAL\Exception\InvalidArgumentException;

class DBTestCaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\DBTestCase::setUp
     */
    public function testSetUp()
    {
        $testCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\DBTestCase')
                           ->disableOriginalConstructor()
                           ->setMethods(['getFixtureManager'])
                           ->getMockForAbstractClass()
        ;

        $testCase->expects($this->once())->method('getFixtureManager');

        $reflectionClass = new \ReflectionClass($testCase);

        $classProperty = $reflectionClass->getParentClass()->getParentClass()->getProperty('class');
        $classProperty->setAccessible(true);
        $classProperty->setValue($testCase, 'Cosma\Bundle\TestingBundle\Tests\AppKernel');

        $method = $reflectionClass->getMethod('setUp');
        $method->setAccessible(true);
        $method->invoke($testCase);

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernel = $kernelProperty->getValue();

        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\AppKernel', $kernel, 'set up is wrong');
    }
}


