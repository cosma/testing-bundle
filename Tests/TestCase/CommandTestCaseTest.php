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

use Cosma\Bundle\TestingBundle\TestCase\CommandTestCase;

class CommandTestCaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see CommandTestCase
     */
    public function testStaticAttributes()
    {
        $this->assertClassHasAttribute('application', 'Cosma\Bundle\TestingBundle\TestCase\CommandTestCase');
    }

    /**
     * @see CommandTestCase::setUp
     */
    public function testSetUp()
    {
        $commandTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\CommandTestCase')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $reflectionClassMocked = new \ReflectionClass($commandTestCase);
        $reflectionClass = $reflectionClassMocked->getParentClass()->getParentClass();

        $classProperty = $reflectionClass->getProperty('class');
        $classProperty->setAccessible(TRUE);
        $classProperty->setValue($commandTestCase, 'Cosma\Bundle\TestingBundle\Tests\AppKernel');


        $setUpMethod = $reflectionClass->getMethod('setUp');
        $setUpMethod->setAccessible(TRUE);
        $setUpMethod->invoke($commandTestCase);

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(TRUE);
        $kernel = $kernelProperty->getValue();

        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\AppKernel', $kernel, 'set up is wrong');

        $applicationProperty = $reflectionClassMocked->getParentClass()->getProperty('application');
        $applicationProperty->setAccessible(TRUE);
        $applicationProperty->getValue($commandTestCase);

        $reflectionMethod = $reflectionClass->getMethod('tearDownAfterClass');
        $reflectionMethod->invoke($commandTestCase);
    }
}

class CommandTestCaseExample extends CommandTestCase
{
}



