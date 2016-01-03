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

class CommandTestCaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see \Cosma\Bundle\TestingBundle\TestCase\CommandTestCase::setUp
     */
    public function testSetUp()
    {
        $testCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\CommandTestCase')
                         ->disableOriginalConstructor()
                         ->setMethods(['getConsoleApplication'])
                         ->getMockForAbstractClass()
        ;

        $testCase->expects($this->once())
                 ->method('getConsoleApplication')
        ;

        $reflectionClass = new \ReflectionClass($testCase);

        $classProperty = $reflectionClass->getParentClass()->getProperty('class');
        $classProperty->setAccessible(true);
        $classProperty->setValue($testCase, '\Cosma\Bundle\TestingBundle\Tests\AppKernel');

        $setUpMethod = $reflectionClass->getMethod('setUp');
        $setUpMethod->setAccessible(true);
        $setUpMethod->invoke($testCase);
    }
}




