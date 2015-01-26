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

use Cosma\Bundle\TestingBundle\Command\FixturesDumpCommand;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;

class FixturesDumpCommandTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @see FixturesDumpCommand::execute
     */
    public function testExecute()
    {
        $dumper = $this->getMockBuilder('Cosma\Bundle\TestingBundle\Fixture\Dumper')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $doctrine = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->setMethods(array('getManager'))
            ->getMock();
        $doctrine->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($entityManager));

        $valueMap = array(
            array('doctrine', $doctrine),
            array('cosma_testing.fixture_dumper', $dumper)
        );


        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();
        $container->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($valueMap));


        $command = $this->getMockBuilder('Cosma\Bundle\TestingBundle\Command\FixturesDumpCommand')
            ->disableOriginalConstructor()
            ->setMethods(array('getContainer'))
            ->getMock();
        $command->expects($this->exactly(2))
            ->method('getContainer')
            ->will($this->returnValue($container));


        $input = new StringInput('cosma_testing:fixtures:dump');

        $temporaryFile = tmpfile();
        $output = new StreamOutput($temporaryFile);

        $reflectionClass= new \ReflectionClass($command);

        $executeMethod = $reflectionClass->getMethod('execute');
        $executeMethod->setAccessible(TRUE);
        $executeMethod->invoke($command, $input, $output);



    }
}
