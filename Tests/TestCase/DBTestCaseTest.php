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
        $DBTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\DBTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClassMocked = new \ReflectionClass($DBTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $classProperty = $reflectionClass->getProperty('class');
        $classProperty->setAccessible(true);
        $classProperty->setValue($DBTestCase, 'Cosma\Bundle\TestingBundle\Tests\AppKernel');

        $setUpMethod = $reflectionClass->getMethod('setUp');
        $setUpMethod->setAccessible(true);
        $setUpMethod->invoke($DBTestCase);

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernel = $kernelProperty->getValue();

        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\AppKernel', $kernel, 'set up is wrong');
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\DBTestCase::getKernel
     */
    public function testGetKernel()
    {
        $DBTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\DBTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClass = new \ReflectionClass($DBTestCase);

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernelProperty->setValue(null);

        $reflectionMethod = $reflectionClass->getMethod('getKernel');
        $reflectionMethod->setAccessible(true);

        $kernel = $reflectionMethod->invoke($DBTestCase);

        $this->assertInstanceOf(
            'Symfony\Component\HttpKernel\HttpKernelInterface',
            $kernel,
            'must return a kernel object'
        );
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\DBTestCase::getContainer
     */
    public function testGetContainer()
    {
        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
                          ->disableOriginalConstructor()
                          ->getMockForAbstractClass()
        ;

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getContainer'])
                       ->getMockForAbstractClass()
        ;
        $kernel->expects($this->once())
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;

        $DBTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\DBTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClass = new \ReflectionClass($DBTestCase);

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernelProperty->setValue($kernel);

        $reflectionMethod = $reflectionClass->getMethod('getContainer');
        $reflectionMethod->setAccessible(true);

        $container = $reflectionMethod->invoke($DBTestCase);

        $this->assertInstanceOf(
            'Symfony\Component\DependencyInjection\ContainerInterface',
            $container,
            'must return a Container object'
        );
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\DBTestCase::getClient
     */
    public function testGetClient()
    {
        $client = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Client')
                       ->disableOriginalConstructor()
                       ->getMock()
        ;

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
                          ->disableOriginalConstructor()
                          ->setMethods(['get'])
                          ->getMockForAbstractClass()
        ;
        $container->expects($this->once())
                  ->method('get')
                  ->with('test.client')
                  ->will($this->returnValue($client))
        ;

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getContainer'])
                       ->getMockForAbstractClass()
        ;
        $kernel->expects($this->once())
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;

        $DBTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\DBTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClass = new \ReflectionClass($DBTestCase);

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernelProperty->setValue($kernel);

        $reflectionMethod = $reflectionClass->getMethod('getClient');
        $reflectionMethod->setAccessible(true);

        $client = $reflectionMethod->invoke($DBTestCase);

        $this->assertInstanceOf(
            'Symfony\Bundle\FrameworkBundle\Client',
            $client,
            'must return a Client object'
        );
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\DBTestCase::getMockedEntityWithId
     * @expectedException \Doctrine\ORM\EntityNotFoundException
     */
    public function testGetMockedEntityWithId_Exception()
    {
        $DBTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\DBTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClass  = new \ReflectionClass($DBTestCase);
        $reflectionMethod = $reflectionClass->getMethod('getMockedEntityWithId');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($DBTestCase, 'Cosma\Bundle\TestingBundle\xxx', 12345);
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\DBTestCase::getMockedEntityWithId
     */
    public function testGetMockedEntityWithId_FullNamespace()
    {
        $DBTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\DBTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClass  = new \ReflectionClass($DBTestCase);
        $reflectionMethod = $reflectionClass->getMethod('getMockedEntityWithId');
        $reflectionMethod->setAccessible(true);

        /** @var ExampleEntity $mockedEntity */
        $mockedEntity = $reflectionMethod->invoke(
            $DBTestCase,
            'Cosma\Bundle\TestingBundle\Tests\SomeEntity',
            12345
        );

        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\SomeEntity', $mockedEntity);
        $this->assertEquals(12345, $mockedEntity->getId());
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\DBTestCase::getMockedEntityWithId
     */
    public function testGetMockedEntityWithId_BundleAndEntity()
    {
        $entityRepository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')
                                 ->disableOriginalConstructor()
                                 ->setMethods(['getClassName'])
                                 ->getMock()
        ;
        $entityRepository->expects($this->any())
                         ->method('getClassName')
                         ->will($this->returnValue('Cosma\Bundle\TestingBundle\Tests\AnotherExampleEntity'))
        ;

        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
                              ->disableOriginalConstructor()
                              ->setMethods(['getRepository'])
                              ->getMock()
        ;

        $entityManager->expects($this->once())
                      ->method('getRepository')
                      ->will($this->returnValue($entityRepository))
        ;

        $DBTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\DBTestCase')
                            ->disableOriginalConstructor()
                            ->setMethods(['getEntityManager'])
                            ->getMockForAbstractClass()
        ;

        $DBTestCase->expects($this->once())
                    ->method('getEntityManager')
                    ->will($this->returnValue($entityManager))
        ;

        $reflectionClassMocked = new \ReflectionClass($DBTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $reflectionMethod = $reflectionClass->getMethod('getMockedEntityWithId');
        $reflectionMethod->setAccessible(true);

        /** @var AnotherExampleEntity $mockedEntity */
        $mockedEntity = $reflectionMethod->invoke($DBTestCase, 'TestingBundle:AnotherExampleEntity', 4567);

        $this->assertEquals(4567, $mockedEntity->getId());
        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\AnotherExampleEntity', $mockedEntity);
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\DBTestCase::getEntityWithId
     * @expectedException \Doctrine\ORM\EntityNotFoundException
     */
    public function testGetEntityWithId_Exception()
    {
        $DBTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\DBTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClass  = new \ReflectionClass($DBTestCase);
        $reflectionMethod = $reflectionClass->getMethod('getEntityWithId');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($DBTestCase, 'Cosma\Bundle\TestingBundle\xxx', 12345);
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\DBTestCase::getEntityWithId
     */
    public function testGetEntityWithId_FullNamespace()
    {
        $DBTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\DBTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClass  = new \ReflectionClass($DBTestCase);
        $reflectionMethod = $reflectionClass->getMethod('getEntityWithId');
        $reflectionMethod->setAccessible(true);

        /** @var ExampleEntity $entity */
        $entity = $reflectionMethod->invoke($DBTestCase, 'Cosma\Bundle\TestingBundle\Tests\SomeEntity', 12345);

        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\SomeEntity', $entity);

        $this->assertEquals(12345, $entity->getId());
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\DBTestCase::getEntityWithId
     */
    public function testGetEntityWithId_BundleAndEntity()
    {
        $entityRepository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')
                                 ->disableOriginalConstructor()
                                 ->setMethods(['getClassName'])
                                 ->getMock()
        ;
        $entityRepository->expects($this->any())
                         ->method('getClassName')
                         ->will($this->returnValue('Cosma\Bundle\TestingBundle\Tests\AnotherExampleEntity'))
        ;

        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
                              ->disableOriginalConstructor()
                              ->setMethods(['getRepository'])
                              ->getMock()
        ;

        $entityManager->expects($this->once())
                      ->method('getRepository')
                      ->will($this->returnValue($entityRepository))
        ;

        $DBTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\DBTestCase')
                            ->disableOriginalConstructor()
                            ->setMethods(['getEntityManager'])
                            ->getMockForAbstractClass()
        ;

        $DBTestCase->expects($this->once())
                    ->method('getEntityManager')
                    ->will($this->returnValue($entityManager))
        ;

        $reflectionClassMocked = new \ReflectionClass($DBTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $reflectionMethod = $reflectionClass->getMethod('getEntityWithId');
        $reflectionMethod->setAccessible(true);

        /** @var AnotherExampleEntity $entity */
        $entity = $reflectionMethod->invoke($DBTestCase, 'TestingBundle:AnotherExampleEntity', 12345);

        $this->assertEquals(12345, $entity->getId());
        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\AnotherExampleEntity', $entity);
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\DBTestCase::loadTableFixtures
     *
     * @expectedException InvalidArgumentException
     */
    public function testLoadTableFixtures_Exception()
    {
        $DBTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\DBTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClass = new \ReflectionClass($DBTestCase);
        $method          = $reflectionClass->getParentClass()->getMethod('loadFixtures');
        $method->setAccessible(true);

        $method->invoke($DBTestCase, []);
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\DBTestCase::loadTableFixtures
     */
    public function testLoadTableFixtures()
    {
        $fixtureManager = $this->getMockBuilder('h4cc\AliceFixturesBundle\Fixtures\FixtureManagerInterface')
                               ->disableOriginalConstructor()
                               ->setMethods(['persist', 'loadFiles'])
                               ->getMockForAbstractClass()
        ;
        $fixtureManager->expects($this->atLeast(2))
                       ->method('persist')
                       ->will($this->returnValue(true))
        ;
        $fixtureManager->expects($this->once())
                       ->method('loadFiles')
                       ->with([
                                  'Cosma/Bundle/TestingBundle/FixtureDirectory/Table/SomeEntity.yml',
                                  'Cosma/Bundle/TestingBundle/FixtureDirectory/Table/AnotherExampleEntity.yml'
                              ])
                       ->will($this->returnValue($this->getEntities()))
        ;

        $valueMap  = [
            ['cosma_testing.fixture_directory', 'FixtureDirectory']
        ];
        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
                          ->disableOriginalConstructor()
                          ->setMethods(['get', 'getParameter'])
                          ->getMockForAbstractClass()
        ;
        $container->expects($this->atLeast(2))
                  ->method('getParameter')
                  ->will($this->returnValueMap($valueMap))
        ;
        $container->expects($this->once())
                  ->method('get')
                  ->with('h4cc_alice_fixtures.manager')
                  ->will($this->returnValue($fixtureManager))
        ;

        $currentBundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\BundleInterface')
                              ->disableOriginalConstructor()
                              ->setMethods(['getPath'])
                              ->getMockForAbstractClass()
        ;
        $currentBundle->expects($this->once())
                      ->method('getPath')
                      ->will($this->returnValue('Cosma/Bundle/TestingBundle'))
        ;

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getContainer', 'getBundles'])
                       ->getMockForAbstractClass()
        ;
        $kernel->expects($this->atLeast(3))
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;

        $DBTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\DBTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClass = new \ReflectionClass($DBTestCase);

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernelProperty->setValue($kernel);

        $currentBundleProperty = $reflectionClass->getParentClass()->getProperty('currentBundle');
        $currentBundleProperty->setAccessible(true);
        $currentBundleProperty->setValue($currentBundle);

        $loadTableFixturesMethod = $reflectionClass->getParentClass()->getMethod('loadFixtures');
        $loadTableFixturesMethod->setAccessible(true);

        $entities = $loadTableFixturesMethod->invoke($DBTestCase, ['SomeEntity', 'AnotherExampleEntity']);

        $reflectionMethod = $reflectionClass->getMethod('tearDownAfterClass');
        $reflectionMethod->invoke(null);

        $this->assertEquals($this->getEntities(), $entities, 'Entities are wrong');
    }

    /**
     * @return array
     */
    private function getEntities()
    {
        $objects = [];

        $entityOne = new SomeEntity();
        $entityOne->setName('Some Entity One');
        array_push($objects, $entityOne);

        $entityTwo = new SomeEntity();
        $entityTwo->setName('Some Entity Two');
        array_push($objects, $entityTwo);

        $entityThree = new AnotherExampleEntity();
        $entityThree->setFirstName('Example Entity Three');
        array_push($objects, $entityThree);

        $entityFour = new AnotherExampleEntity();
        $entityFour->setFirstName('Example Entity Four');
        array_push($objects, $entityFour);

        return $objects;
    }
}


