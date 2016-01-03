<?php

/**
 * This file is part of the "cosma/testing-bundle" project
 *
 * (c) Cosmin Voicu<cosmin.voicu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 01/26/15
 * Time: 23:33
 */

namespace Cosma\Bundle\TestingBundle\Tests\Command;

use Cosma\Bundle\TestingBundle\Command\FixturesImportCommand;
use h4cc\AliceFixturesBundle\Fixtures\FixtureSet;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Container;

class FixturesImportCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see FixturesImportCommand::configure
     */
    public function testConfigure()
    {
        $command = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\Command\FixturesExportCommand')
                        ->disableOriginalConstructor()
                        ->setMethods(['getContainer', 'setName', 'setDescription', 'addArgument', 'addOption', 'setHelp'])
                        ->getMock()
        ;

        $command->expects($this->once())
                ->method('setName')
                ->will($this->returnSelf())
        ;
        $command->expects($this->once())
                ->method('setDescription')
                ->will($this->returnSelf())
        ;
        $command->expects($this->exactly(2))
                ->method('addArgument')
                ->will($this->returnSelf())
        ;
        $command->expects($this->once())
                ->method('addOption')
                ->will($this->returnSelf())
        ;
        $command->expects($this->once())
                ->method('setHelp')
                ->will($this->returnSelf())
        ;

        $reflectionClass = new \ReflectionClass($command);

        $configureMethod = $reflectionClass->getMethod('configure');
        $configureMethod->setAccessible(true);
        $configureMethod->invoke($command);
    }

    /**
     * @see FixturesImportCommand::execute
     */
    public function testExecute()
    {
        $managerMock = $this->getMockBuilder('\h4cc\AliceFixturesBundle\Fixtures\FixtureManagerInterface')
                            ->setMethods(['load', 'createFixtureSet'])
                            ->getMockForAbstractClass()
        ;

        $managerMock->expects($this->any())->method('createFixtureSet')->will(
            $this->returnValue(new FixtureSet())
        )
        ;

        $application = new Application();
        $application->add(new FixturesImportCommand());

        $container = new Container();
        $container->set('h4cc_alice_fixtures.manager', $managerMock);

        /** @type FixturesImportCommand $command */
        $command = $application->find('cosma_testing:fixtures:import');
        $command->setContainer($container);

        $this->assertEquals(['h4cc_alice_fixtures:load:files'], $command->getAliases(), 'command is wrong');
    }
}
