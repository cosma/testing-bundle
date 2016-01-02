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

use Cosma\Bundle\TestingBundle\Tests\AnotherExampleEntity;
use Doctrine\ORM\EntityRepository;
use SebastianBergmann\GlobalState\Exception;
use Symfony\Component\DependencyInjection\Container;

class DBTestTraitTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @see \Cosma\Bundle\TestingBundle\TestCase\Traits\DBTestTrait::getEntityManager
     */
    public function testGetEntityManager()
    {
        $container = new Container();

        $entityManager = $this->prophesize('\Doctrine\ORM\EntityManager');

        $container->set('doctrine.orm.entity_manager', $entityManager->reveal());

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\HttpKernelInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getContainer'])
                       ->getMockForAbstractClass()
        ;
        $kernel->expects($this->once())
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;

        $mockDBTrait = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\TestCase\Traits\DBTestTrait')
                            ->disableOriginalConstructor()
                            ->setMethods(['getKernel'])
                            ->getMockForTrait()
        ;
        $mockDBTrait->expects($this->once())
                    ->method('getKernel')
                    ->will($this->returnValue($kernel))
        ;

        $reflectionClass = new \ReflectionClass($mockDBTrait);

        $reflectionMethod = $reflectionClass->getMethod('getEntityManager');
        $reflectionMethod->setAccessible(true);
        $entityManager = $reflectionMethod->invoke($mockDBTrait);

        $this->assertInstanceOf('\Doctrine\ORM\EntityManager', $entityManager);
    }

    /**
     * @see \Cosma\Bundle\TestingBundle\TestCase\Traits\DBTestTrait::getEntityRepository
     */
    public function testGetEntityRepository()
    {
        $mockDBTrait = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\TestCase\Traits\DBTestTrait')
                            ->disableOriginalConstructor()
                            ->setMethods(['getEntityManager'])
                            ->getMockForTrait()
        ;

        $entityRepository = $this->prophesize('\Doctrine\ORM\EntityRepository');

        /** @type EntityRepository $entityManager */
        $entityManager = $this->prophesize('\Doctrine\ORM\EntityManager');
        $entityManager->getRepository('AppBundle:ExampleEntity')->willReturn($entityRepository);

        $mockDBTrait->expects($this->once())
                    ->method('getEntityManager')
                    ->will($this->returnValue($entityManager->reveal()))
        ;

        $reflectionClass = new \ReflectionClass($mockDBTrait);

        $reflectionMethod = $reflectionClass->getMethod('getEntityRepository');
        $reflectionMethod->setAccessible(true);
        $entityRepository = $reflectionMethod->invoke($mockDBTrait, 'AppBundle:ExampleEntity');

        $this->assertInstanceOf('\Doctrine\ORM\EntityRepository', $entityRepository);
    }

    /**
     * @see \Cosma\Bundle\TestingBundle\TestCase\Traits\DBTestTrait::dropDatabase
     */
    public function testDropDatabase()
    {
        $fixtureManager = $this->getMockBuilder('\h4cc\AliceFixturesBundle\Fixture\FixtureManager')
                               ->disableOriginalConstructor()
                               ->setMethods(['persist'])
                               ->getMock()
        ;
        $fixtureManager->expects($this->once())
                       ->method('persist')
                       ->with([], true)
                       ->will($this->returnValue(null))
        ;

        $mockDBTrait = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\TestCase\Traits\DBTestTrait')
                            ->disableOriginalConstructor()
                            ->getMockForTrait()
        ;

        $reflectionClass        = new \ReflectionClass($mockDBTrait);
        $fixtureManagerProperty = $reflectionClass->getProperty('fixtureManager');
        $fixtureManagerProperty->setAccessible(true);
        $fixtureManagerProperty->setValue($mockDBTrait, $fixtureManager);

        $reflectionMethod = $reflectionClass->getMethod('dropDatabase');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($mockDBTrait);
    }

    /**
     * @see \Cosma\Bundle\TestingBundle\TestCase\Traits\DBTestTrait::getFixtureManager
     */
    public function testGetFixtureManager()
    {
        $container = new Container();

        $mockedFixtureManager = $this->prophesize('\h4cc\AliceFixturesBundle\Fixture\FixtureManager');

        $container->set('h4cc_alice_fixtures.manager', $mockedFixtureManager->reveal());

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\HttpKernelInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getContainer'])
                       ->getMockForAbstractClass()
        ;
        $kernel->expects($this->once())
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;

        $mockDBTrait = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\TestCase\Traits\DBTestTrait')
                            ->disableOriginalConstructor()
                            ->setMethods(['getKernel'])
                            ->getMockForTrait()
        ;
        $mockDBTrait->expects($this->once())
                    ->method('getKernel')
                    ->will($this->returnValue($kernel))
        ;

        $reflectionClass = new \ReflectionClass($mockDBTrait);

        $reflectionMethod = $reflectionClass->getMethod('getFixtureManager');
        $reflectionMethod->setAccessible(true);
        $fixtureManager = $reflectionMethod->invoke($mockDBTrait);

        $this->assertInstanceOf('\h4cc\AliceFixturesBundle\Fixture\FixtureManager', $fixtureManager);
    }

    /**
     * @see \Cosma\Bundle\TestingBundle\TestCase\Traits\DBTestTrait::loadFixtures
     */
    public function testLoadFixtures_WithDropDB()
    {
        $container = new Container();

        $container->setParameter('cosma_testing.fixture_directory', 'Fixture');

        $fixtureManager = $this->getMockBuilder('\h4cc\AliceFixturesBundle\Fixture\FixtureManager')
                               ->disableOriginalConstructor()
                               ->setMethods(['persist', 'loadFiles'])
                               ->getMock()
        ;
        $fixtureManager->expects($this->exactly(2))
                       ->method('persist')
                       ->will($this->returnValue(null))
        ;
        $fixtureManager->expects($this->once())
                       ->method('loadFiles')
                       ->with([
                                  'src/path/to/bundle/AppBundle/Fixture/Subdirectory/Entity.yml',
                                  'src/path/to/bundle/AnotherBundle/Fixture/AnotherSubdirectory/AnotherEntity.yml'
                              ])
                       ->will($this->returnValue([new AnotherExampleEntity()]))
        ;

        $appBundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\Bundle')
                          ->disableOriginalConstructor()
                          ->setMethods(['getPath'])
                          ->getMockForAbstractClass()
        ;
        $appBundle->expects($this->once())
                  ->method('getPath')
                  ->will($this->returnValue('src/path/to/bundle/AppBundle'))
        ;

        $anotherBundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\Bundle')
                              ->disableOriginalConstructor()
                              ->setMethods(['getPath'])
                              ->getMockForAbstractClass()
        ;
        $anotherBundle->expects($this->once())
                      ->method('getPath')
                      ->will($this->returnValue('src/path/to/bundle/AnotherBundle'))
        ;

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\HttpKernelInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getBundle', 'getContainer'])
                       ->getMockForAbstractClass()
        ;
        $kernel->expects($this->exactly(2))
               ->method('getBundle')
               ->will($this->onConsecutiveCalls($appBundle, $anotherBundle))
        ;
        $kernel->expects($this->exactly(2))
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;

        $mockDBTrait = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\TestCase\Traits\DBTestTrait')
                            ->disableOriginalConstructor()
                            ->setMethods(['getKernel'])
                            ->getMockForTrait()
        ;

        $mockDBTrait->expects($this->exactly(4))
                    ->method('getKernel')
                    ->will($this->returnValue($kernel))
        ;

        $reflectionClass        = new \ReflectionClass($mockDBTrait);
        $fixtureManagerProperty = $reflectionClass->getProperty('fixtureManager');
        $fixtureManagerProperty->setAccessible(true);
        $fixtureManagerProperty->setValue($mockDBTrait, $fixtureManager);

        $reflectionMethod = $reflectionClass->getMethod('loadFixtures');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($mockDBTrait,
                                  ['AppBundle:Subdirectory:Entity', 'AnotherBundle:AnotherSubdirectory:AnotherEntity']);
    }


    /**
     * @see \Cosma\Bundle\TestingBundle\TestCase\Traits\DBTestTrait::loadFixtures
     */
    public function testLoadFixtures_WithoutDropDB()
    {
        $container = new Container();

        $container->setParameter('cosma_testing.fixture_directory', 'Fixture');

        $fixtureManager = $this->getMockBuilder('\h4cc\AliceFixturesBundle\Fixture\FixtureManager')
                               ->disableOriginalConstructor()
                               ->setMethods(['persist', 'loadFiles'])
                               ->getMock()
        ;
        $fixtureManager->expects($this->once())
                       ->method('persist')
                       ->will($this->returnValue(null))
        ;
        $fixtureManager->expects($this->once())
                       ->method('loadFiles')
                       ->with([
                                  'src/path/to/bundle/AppBundle/Fixture/Subdirectory/Entity.yml',
                                  'src/path/to/bundle/AnotherBundle/Fixture/AnotherSubdirectory/AnotherEntity.yml'
                              ])
                       ->will($this->returnValue([new AnotherExampleEntity()]))
        ;

        $appBundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\Bundle')
                          ->disableOriginalConstructor()
                          ->setMethods(['getPath'])
                          ->getMockForAbstractClass()
        ;
        $appBundle->expects($this->once())
                  ->method('getPath')
                  ->will($this->returnValue('src/path/to/bundle/AppBundle'))
        ;

        $anotherBundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\Bundle')
                              ->disableOriginalConstructor()
                              ->setMethods(['getPath'])
                              ->getMockForAbstractClass()
        ;
        $anotherBundle->expects($this->once())
                      ->method('getPath')
                      ->will($this->returnValue('src/path/to/bundle/AnotherBundle'))
        ;

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\HttpKernelInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getBundle', 'getContainer'])
                       ->getMockForAbstractClass()
        ;
        $kernel->expects($this->exactly(2))
               ->method('getBundle')
               ->will($this->onConsecutiveCalls($appBundle, $anotherBundle))
        ;
        $kernel->expects($this->exactly(2))
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;

        $mockDBTrait = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\TestCase\Traits\DBTestTrait')
                            ->disableOriginalConstructor()
                            ->setMethods(['getKernel'])
                            ->getMockForTrait()
        ;

        $mockDBTrait->expects($this->exactly(4))
                    ->method('getKernel')
                    ->will($this->returnValue($kernel))
        ;

        $reflectionClass        = new \ReflectionClass($mockDBTrait);
        $fixtureManagerProperty = $reflectionClass->getProperty('fixtureManager');
        $fixtureManagerProperty->setAccessible(true);
        $fixtureManagerProperty->setValue($mockDBTrait, $fixtureManager);

        $reflectionMethod = $reflectionClass->getMethod('loadFixtures');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($mockDBTrait,
                                  ['AppBundle:Subdirectory:Entity', 'AnotherBundle:AnotherSubdirectory:AnotherEntity'], false);
    }

    /**
     * @see \Cosma\Bundle\TestingBundle\TestCase\Traits\DBTestTrait::loadFixtures
     * @expectedException \Exception
     */
    public function testLoadFixtures_Exception()
    {
        $container = new Container();

        $container->setParameter('cosma_testing.fixture_directory', 'Fixture');

        $fixtureManager = $this->getMockBuilder('\h4cc\AliceFixturesBundle\Fixture\FixtureManager')
                               ->disableOriginalConstructor()
                               ->setMethods(['persist', 'loadFiles'])
                               ->getMock()
        ;

        $mockDBTrait = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\TestCase\Traits\DBTestTrait')
                            ->disableOriginalConstructor()
                            ->setMethods(['getKernel'])
                            ->getMockForTrait()
        ;

        $reflectionClass        = new \ReflectionClass($mockDBTrait);
        $fixtureManagerProperty = $reflectionClass->getProperty('fixtureManager');
        $fixtureManagerProperty->setAccessible(true);
        $fixtureManagerProperty->setValue($mockDBTrait, $fixtureManager);

        $reflectionMethod = $reflectionClass->getMethod('loadFixtures');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($mockDBTrait,
                                  ['WrongBundle', 'AppBundle:Subdirectory:Entity']);
    }

}