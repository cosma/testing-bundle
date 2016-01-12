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
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\DependencyInjection\Container;

class FixturesDumpCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers FixturesDumpCommand::configure
     */
    public function testConfigure()
    {
        $command = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\Command\FixturesDumpCommand')
            ->disableOriginalConstructor()
            ->setMethods(array('getContainer', 'setName', 'setDescription', 'addArgument', 'addOption', 'setHelp'))
            ->getMock();

        $command->expects($this->once())
            ->method('setName')
            ->will($this->returnSelf());
        $command->expects($this->once())
            ->method('setDescription')
            ->will($this->returnSelf());
        $command->expects($this->exactly(2))
            ->method('addArgument')
            ->will($this->returnSelf());
        $command->expects($this->once())
            ->method('addOption')
            ->will($this->returnSelf());
        $command->expects($this->once())
            ->method('setHelp')
            ->will($this->returnSelf());

        $reflectionClass = new \ReflectionClass($command);

        $configureMethod = $reflectionClass->getMethod('configure');
        $configureMethod->setAccessible(TRUE);
        $configureMethod->invoke($command);
    }

    /**
     * @covers FixturesDumpCommand::execute
     */
    public function testExecute_SpecificEntity_Association()
    {
        $directoryPath = sys_get_temp_dir();

        $dumper = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\Fixture\Dumper')
            ->disableOriginalConstructor()
            ->setMethods(array('dumpToYaml', 'setAssociation', 'setClassMetadataInfo'))
            ->getMock();
        $dumper->expects($this->once())
            ->method('dumpToYaml')
            ->with($directoryPath)
            ->will($this->returnValue($directoryPath . '/table.yml'));
        $dumper->expects($this->once())
            ->method('setAssociation')
            ->with(TRUE)
            ->will($this->returnValue(NULL));

        $classMetaDataInfo = $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadataInfo')
            ->disableOriginalConstructor()
            ->getMock();

        $dumper->expects($this->once())
            ->method('setClassMetadataInfo')
            ->with($classMetaDataInfo)
            ->will($this->returnValue(NULL));

        $metaDataFactory = $this->getMockBuilder('\Doctrine\Common\Persistence\Mapping\ClassMetadataFactory')
            ->disableOriginalConstructor()
            ->setMethods(array('getMetadataFor'))
            ->getMockForAbstractClass();
        $metaDataFactory->expects($this->once())
            ->method('getMetadataFor')
            ->with('BundleName:EntityName')
            ->will($this->returnValue($classMetaDataInfo));

        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getMetadataFactory'))
            ->getMock();
        $entityManager->expects($this->once())
            ->method('getMetadataFactory')
            ->will($this->returnValue($metaDataFactory));

        $doctrine = $this->getMockBuilder('\Doctrine\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->setMethods(array('getManager'))
            ->getMock();
        $doctrine->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($entityManager));

        $container = new Container();
        $container->set('doctrine', $doctrine);
        $container->set('cosma_testing.fixture_dumper', $dumper);

        $command = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\Command\FixturesDumpCommand')
            ->disableOriginalConstructor()
            ->setMethods(array('getContainer'))
            ->getMock();
        $command->expects($this->exactly(2))
            ->method('getContainer')
            ->will($this->returnValue($container));

        $reflectionClass = new \ReflectionClass($command);

        $dumperProperty = $reflectionClass->getParentClass()->getProperty('dumper');
        $dumperProperty->setAccessible(TRUE);
        $dumperProperty->setValue($command, $dumper);

        $inputDefinition = new InputDefinition(array(
            new InputArgument('dumpDirectory', InputArgument::REQUIRED),
            new InputArgument('entity', InputArgument::OPTIONAL),
            new InputOption('associations', 'a', InputOption::VALUE_NONE),
        ));

        $input = new ArgvInput(
            array(
                'dummySoInputValidates' => 'dummy',
                'dumpDirectory'         => $directoryPath,
                'entity'                => 'BundleName:EntityName',
            ),
            $inputDefinition);

        $input->setOption('associations', TRUE);

        $output = new BufferedOutput();

        $reflectionClass = new \ReflectionClass($command);

        $executeMethod = $reflectionClass->getMethod('execute');
        $executeMethod->setAccessible(TRUE);
        $executeMethod->invoke($command, $input, $output);

        $this->assertContains("successfully dumped in file  {$directoryPath}/table.yml", $output->fetch(), 'The entity was not dump successfully');
    }

    /**
     * @covers FixturesDumpCommand::execute
     */
    public function testExecute_NoSpecificEntity_NoAssociation()
    {
        $directoryPath = sys_get_temp_dir();

        $dumper = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\Fixture\Dumper')
            ->disableOriginalConstructor()
            ->setMethods(array('dumpToYaml', 'setAssociation', 'setClassMetadataInfo'))
            ->getMock();
        $dumper->expects($this->exactly(2))
            ->method('dumpToYaml')
            ->with($directoryPath)
            ->will($this->returnValue($directoryPath . '/table.yml'));
        $dumper->expects($this->once())
            ->method('setAssociation')
            ->with(FALSE)
            ->will($this->returnValue(NULL));

        $classMetaDataInfo = $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadataInfo')
            ->disableOriginalConstructor()
            ->getMock();

        $classMetaDataInfo2 = $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadataInfo')
            ->disableOriginalConstructor()
            ->getMock();

        $dumper->expects($this->exactly(2))
            ->method('setClassMetadataInfo')
            ->will($this->returnValue(NULL));

        $metaDataFactory = $this->getMockBuilder('\Doctrine\Common\Persistence\Mapping\ClassMetadataFactory')
            ->disableOriginalConstructor()
            ->setMethods(array('getAllMetadata'))
            ->getMockForAbstractClass();
        $metaDataFactory->expects($this->once())
            ->method('getAllMetadata')
            ->will($this->returnValue(array($classMetaDataInfo, $classMetaDataInfo2)));

        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getMetadataFactory'))
            ->getMock();
        $entityManager->expects($this->once())
            ->method('getMetadataFactory')
            ->will($this->returnValue($metaDataFactory));

        $doctrine = $this->getMockBuilder('\Doctrine\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->setMethods(array('getManager'))
            ->getMock();
        $doctrine->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($entityManager));

        $container = new Container();
        $container->set('doctrine', $doctrine);
        $container->set('cosma_testing.fixture_dumper', $dumper);

        $command = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\Command\FixturesDumpCommand')
            ->disableOriginalConstructor()
            ->setMethods(array('getContainer'))
            ->getMock();
        $command->expects($this->exactly(2))
            ->method('getContainer')
            ->will($this->returnValue($container));


        $inputDefinition = new InputDefinition(array(
            new InputArgument('dumpDirectory', InputArgument::REQUIRED),
            new InputArgument('entity', InputArgument::OPTIONAL),
            new InputOption('associations', 'a', InputOption::VALUE_NONE),
        ));

        $input = new ArgvInput(
            array(
                'dummySoInputValidates' => 'dummy',
                'dumpDirectory'         => $directoryPath
            ),
            $inputDefinition);

        $output = new BufferedOutput();

        $reflectionClass = new \ReflectionClass($command);

        $executeMethod = $reflectionClass->getMethod('execute');
        $executeMethod->setAccessible(TRUE);
        $executeMethod->invoke($command, $input, $output);

        $this->assertContains("successfully dumped in file  {$directoryPath}/table.yml", $output->fetch(), 'The entity was not dump successfully');
    }
}
