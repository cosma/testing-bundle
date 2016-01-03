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

class ElasticTestCaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see \Cosma\Bundle\TestingBundle\TestCase\ElasticTestCase::setUp
     */
    public function testSetUp()
    {
        $testCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\ElasticTestCase')
                         ->disableOriginalConstructor()
                         ->setMethods(['recreateIndex'])
                         ->getMockForAbstractClass()
        ;

        $testCase->expects($this->once())->method('recreateIndex');

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




