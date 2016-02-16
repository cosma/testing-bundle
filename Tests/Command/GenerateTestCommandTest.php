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

use Cosma\Bundle\TestingBundle\Command\GenerateTestCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\Container;

class GenerateTestCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see GenerateTestCommand::configure
     */
    public function testConfigure()
    {
        $command = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\Command\GenerateTestCommand')
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
        $command->expects($this->once())
                ->method('addArgument')
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
     * this test does not work in circleci
     *
     * @group notincircleci
     *
     * @see   GenerateTestCommand::execute
     */
    public function testExecute_Aborted()
    {
        $application = new Application();
        $application->setAutoExit(true);

        $generateCommand = new GenerateTestCommand();

        $container = new Container();

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\Kernel')
                       ->disableOriginalConstructor()
                       ->setMethods(['getRootDir',])
                       ->getMockForAbstractClass()
        ;

        $kernel->expects($this->once())
               ->method('getRootDir')
               ->will($this->returnValue('/some/directory/'))
        ;

        $container->set('kernel', $kernel);

        $generateCommand->setContainer($container);

        $application->add($generateCommand);

        $command = $application->find('cosma_testing:generate:test');

        $commandTester = new CommandTester($command);

        /** @type QuestionHelper $question */
        $question = $command->getHelper('question');
        $question->setInputStream(
            $this->get_console_input_stream(
                "y\n n\n 0\n n\n"
            )
        );

        $commandTester->execute([
                                    'command' => $command->getName(),
                                    'file'    => 'ORM/DoctrineORMSchemaTool.php'
                                ]);

        $this->assertRegExp('/Are you sure you want to rewrite the file/', $commandTester->getDisplay());
        $this->assertRegExp('/The generation for the file (.*) was aborted/', $commandTester->getDisplay());
    }

    /**
     * @see                      GenerateTestCommand::execute
     *
     * @expectedException \Exception
     * @expectedExceptionMessage The file doesn't exist
     */
    public function testExecute_UnexistentFileException()
    {
        $application = new Application();
        $application->setAutoExit(true);
        $application->add(new GenerateTestCommand());

        $command       = $application->find('cosma_testing:generate:test');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
                                    'command' => $command->getName(),
                                    'file'    => 'Some/UnExistentFile.php'
                                ]);

        $this->assertRegExp('/Are you sure you want to rewrite the file/', $commandTester->getDisplay());
        $this->assertRegExp('/The generation for the file (.*) was aborted/', $commandTester->getDisplay());
    }

    /**
     * @see                      GenerateTestCommand::execute
     *
     * @expectedException \Exception
     * @expectedExceptionMessage The file contains no Class or Trait
     */
    public function testExecute_NoCLassException()
    {
        $application = new Application();
        $application->setAutoExit(true);
        $application->add(new GenerateTestCommand());

        $command       = $application->find('cosma_testing:generate:test');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
                                    'command' => $command->getName(),
                                    'file'    => 'Tests/bootstrap.php'
                                ]);

        $this->assertRegExp('/Are you sure you want to rewrite the file/', $commandTester->getDisplay());
        $this->assertRegExp('/The generation for the file (.*) was aborted/', $commandTester->getDisplay());
    }

    /**
     * @param $input
     *
     * @return resource
     */
    function get_console_input_stream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);
        rewind($stream);

        return $stream;
    }
}
