<?php
/**
 * This file is part of the TestingBundle project.
 *
 * @project    TestingBundle
 * @author     Cosmin Voicu <cosmin.voicu@oconotech.com>
 * @copyright  2015 - ocono Tech GmbH
 * @license    http://www.ocono-tech.com proprietary
 * @link       http://www.ocono-tech.com
 * @date       29/12/15
 */

namespace TestCase\Traits;

use Symfony\Component\Console\Command\Command;

class CommandTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see \Cosma\Bundle\TestingBundle\TestCase\Traits\CommandTrait::getConsoleApplication
     */
    public function testGetConsoleApplication()
    {
        $kernel = $this->prophesize('Symfony\Component\HttpKernel\KernelInterface');

        $commandTrait = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\TestCase\Traits\CommandTrait')
                            ->disableOriginalConstructor()
                            ->setMethods(['getKernel'])
                            ->getMockForTrait()
        ;

        $commandTrait->expects($this->once())
                    ->method('getKernel')
                    ->will($this->returnValue($kernel->reveal()))
        ;

        $reflectionClass = new \ReflectionClass($commandTrait);

        $reflectionMethod = $reflectionClass->getMethod('getConsoleApplication');
        $reflectionMethod->setAccessible(true);
        $application = $reflectionMethod->invoke($commandTrait);

        $this->assertInstanceOf('\Symfony\Bundle\FrameworkBundle\Console\Application', $application);
    }

    /**
     * @see \Cosma\Bundle\TestingBundle\TestCase\Traits\CommandTrait::executeCommand
     */
    public function testExecuteCommand()
    {
        $kernel = $this->prophesize('Symfony\Component\HttpKernel\KernelInterface');

        $commandTrait = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\TestCase\Traits\CommandTrait')
                             ->disableOriginalConstructor()
                             ->setMethods(['getKernel'])
                             ->getMockForTrait()
        ;

        $commandTrait->expects($this->once())
                     ->method('getKernel')
                     ->will($this->returnValue($kernel->reveal()))
        ;

        $reflectionClass = new \ReflectionClass($commandTrait);

        $reflectionMethod = $reflectionClass->getMethod('getConsoleApplication');
        $reflectionMethod->setAccessible(true);

        /** @type \Symfony\Bundle\FrameworkBundle\Console\Application $application */
        $application = $reflectionMethod->invoke($commandTrait);

        $command = new Command('some:command');

        $application->add($command);

        $reflectionMethod = $reflectionClass->getMethod('executeCommand');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($commandTrait, 'some:command');
    }
}