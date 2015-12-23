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
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Bundle\FrameworkBundle\Client;

use Cosma\Bundle\TestingBundle\TestCase\WebTestCase;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class DBTestCaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase
     */
    public function testStaticAttributes()
    {
        $this->assertClassHasStaticAttribute('currentBundle', 'Cosma\Bundle\TestingBundle\TestCase\WebTestCase');
        $this->assertClassHasStaticAttribute('fixtureManager', 'Cosma\Bundle\TestingBundle\TestCase\WebTestCase');
        $this->assertClassHasStaticAttribute('fixturePath', 'Cosma\Bundle\TestingBundle\TestCase\WebTestCase');
    }



    /**
     * @see SolrTestCase::setUp
     */
    public function testSetUp()
    {
        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClassMocked = new \ReflectionClass($webTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $classProperty = $reflectionClass->getProperty('class');
        $classProperty->setAccessible(true);
        $classProperty->setValue($webTestCase, 'Cosma\Bundle\TestingBundle\Tests\AppKernel');

        $setUpMethod = $reflectionClass->getMethod('setUp');
        $setUpMethod->setAccessible(true);
        $setUpMethod->invoke($webTestCase);

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernel = $kernelProperty->getValue();

        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\AppKernel', $kernel, 'set up is wrong');

        $reflectionMethod = $reflectionClass->getMethod('tearDownAfterClass');
        $reflectionMethod->invoke($webTestCase);
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::getClient
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

        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClass = new \ReflectionClass($webTestCase);

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernelProperty->setValue($kernel);

        $reflectionMethod = $reflectionClass->getMethod('getClient');
        $reflectionMethod->setAccessible(true);

        /** @var Client $client */
        $client = $reflectionMethod->invoke($webTestCase);

        $this->assertInstanceOf(
            'Symfony\Bundle\FrameworkBundle\Client',
            $client,
            'must return a Client object'
        );
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::getEntityManager
     */
    public function testGetEntityManager()
    {
        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
                              ->disableOriginalConstructor()
                              ->setMethods([])
                              ->getMock()
        ;

        $doctrine = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')
                         ->disableOriginalConstructor()
                         ->setMethods(['getManager'])
                         ->getMock()
        ;
        $doctrine->expects($this->once())
                 ->method('getManager')
                 ->will($this->returnValue($entityManager))
        ;

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
                          ->disableOriginalConstructor()
                          ->setMethods(['get'])
                          ->getMockForAbstractClass()
        ;
        $container->expects($this->once())
                  ->method('get')
                  ->with('doctrine')
                  ->will($this->returnValue($doctrine))
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

        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClass = new \ReflectionClass($webTestCase);

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernelProperty->setValue($kernel);

        $reflectionMethod = $reflectionClass->getMethod('getEntityManager');
        $reflectionMethod->setAccessible(true);

        /** @var EntityManager $entityManager */
        $entityManager = $reflectionMethod->invoke($webTestCase);

        $this->assertInstanceOf(
            'Doctrine\ORM\EntityManager',
            $entityManager,
            'must return a EntityManager object'
        );
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::getEntityRepository
     */
    public function testGetEntityRepository()
    {
        $entityRepository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
                                 ->disableOriginalConstructor()
                                 ->getMock()
        ;

        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
                              ->disableOriginalConstructor()
                              ->setMethods(['getRepository'])
                              ->getMock()
        ;
        $entityManager->expects($this->once())
                      ->method('getRepository')
                      ->with('BundleExample:User')
                      ->will($this->returnValue($entityRepository))
        ;

        $doctrine = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')
                         ->disableOriginalConstructor()
                         ->setMethods(['getManager'])
                         ->getMock()
        ;
        $doctrine->expects($this->once())
                 ->method('getManager')
                 ->will($this->returnValue($entityManager))
        ;

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
                          ->disableOriginalConstructor()
                          ->setMethods(['get'])
                          ->getMockForAbstractClass()
        ;
        $container->expects($this->once())
                  ->method('get')
                  ->with('doctrine')
                  ->will($this->returnValue($doctrine))
        ;

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getContainer', 'getBundles'])
                       ->getMockForAbstractClass()
        ;
        $kernel->expects($this->once())
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;

        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClass = new \ReflectionClass($webTestCase);

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernelProperty->setValue($kernel);

        $reflectionMethod = $reflectionClass->getMethod('getEntityRepository');
        $reflectionMethod->setAccessible(true);

        /** @var EntityRepository $entityRepository */
        $entityRepository = $reflectionMethod->invoke($webTestCase, 'BundleExample:User');

        $this->assertInstanceOf(
            'Doctrine\ORM\EntityRepository',
            $entityRepository,
            'must return a EntityRepository object'
        );
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::getEntityRepository
     */
    public function testGetEntityRepository_ShortName()
    {
        $entityRepository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
                                 ->disableOriginalConstructor()
                                 ->getMock()
        ;

        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
                              ->disableOriginalConstructor()
                              ->setMethods(['getRepository'])
                              ->getMock()
        ;
        $entityManager->expects($this->once())
                      ->method('getRepository')
                      ->with('BundleExample:User')
                      ->will($this->returnValue($entityRepository))
        ;

        $doctrine = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')
                         ->disableOriginalConstructor()
                         ->setMethods(['getManager'])
                         ->getMock()
        ;
        $doctrine->expects($this->once())
                 ->method('getManager')
                 ->will($this->returnValue($entityManager))
        ;

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
                          ->disableOriginalConstructor()
                          ->setMethods(['get'])
                          ->getMockForAbstractClass()
        ;
        $container->expects($this->once())
                  ->method('get')
                  ->with('doctrine')
                  ->will($this->returnValue($doctrine))
        ;

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getContainer', 'getBundles'])
                       ->getMockForAbstractClass()
        ;
        $kernel->expects($this->once())
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;

        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClass = new \ReflectionClass($webTestCase);

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernelProperty->setValue($kernel);

        $currentBundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\BundleInterface')
                              ->disableOriginalConstructor()
                              ->setMethods(['getName'])
                              ->getMockForAbstractClass()
        ;
        $currentBundle->expects($this->once())
                      ->method('getName')
                      ->will($this->returnValue('BundleExample'))
        ;

        $currentBundleProperty = $reflectionClass->getParentClass()->getProperty('currentBundle');
        $currentBundleProperty->setAccessible(true);
        $currentBundleProperty->setValue($currentBundle);

        $reflectionMethod = $reflectionClass->getMethod('getEntityRepository');
        $reflectionMethod->setAccessible(true);

        /** @var EntityRepository $entityRepository */
        $entityRepository = $reflectionMethod->invoke($webTestCase, 'User');

        $this->assertInstanceOf(
            'Doctrine\ORM\EntityRepository',
            $entityRepository,
            'must return a EntityRepository object'
        );
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::getMockedEntityWithId
     * @expectedException \Doctrine\ORM\EntityNotFoundException
     */
    public function testGetMockedEntityWithId_Exception()
    {
        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClass  = new \ReflectionClass($webTestCase);
        $reflectionMethod = $reflectionClass->getMethod('getMockedEntityWithId');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($webTestCase, 'Cosma\Bundle\TestingBundle\xxx', 12345);
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::getMockedEntityWithId
     */
    public function testGetMockedEntityWithId_FullNamespace()
    {
        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClass  = new \ReflectionClass($webTestCase);
        $reflectionMethod = $reflectionClass->getMethod('getMockedEntityWithId');
        $reflectionMethod->setAccessible(true);

        /** @var ExampleEntity $mockedEntity */
        $mockedEntity = $reflectionMethod->invoke(
            $webTestCase,
            'Cosma\Bundle\TestingBundle\Tests\TestCase\SomeEntity',
            12345
        );

        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\TestCase\SomeEntity', $mockedEntity);
        $this->assertEquals(12345, $mockedEntity->getId());
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::getMockedEntityWithId
     */
    public function testGetMockedEntityWithId_BundleAndEntity()
    {
        /** @type ClassMetadata $metadata */
        $metadata            = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
                                    ->disableOriginalConstructor()
                                    ->setMethods(['getAllMetadata'])
                                    ->getMock()
        ;
        $metadata->namespace = 'Cosma\Bundle\TestingBundle\Tests\TestCase';

        $metadataFactory = $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadataFactory')
                                ->disableOriginalConstructor()
                                ->setMethods(['getAllMetadata'])
                                ->getMock()
        ;
        $metadataFactory->expects($this->any())
                        ->method('getAllMetadata')
                        ->will($this->returnValue([$metadata]))
        ;

        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
                              ->disableOriginalConstructor()
                              ->setMethods(['getMetadataFactory'])
                              ->getMock()
        ;
        $entityManager->expects($this->any())
                      ->method('getMetadataFactory')
                      ->will($this->returnValue($metadataFactory))
        ;

        $bundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\BundleInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getName', 'getNamespace'])
                       ->getMockForAbstractClass()
        ;
        $bundle->expects($this->any())
               ->method('getName')
               ->will($this->returnValue('TestingBundle'))
        ;
        $bundle->expects($this->any())
               ->method('getNamespace')
               ->will($this->returnValue('Cosma\Bundle\TestingBundle\Tests\TestCase'))
        ;

        $doctrine = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')
                         ->disableOriginalConstructor()
                         ->setMethods(['getManager'])
                         ->getMock()
        ;
        $doctrine->expects($this->once())
                 ->method('getManager')
                 ->will($this->returnValue($entityManager))
        ;

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
                          ->disableOriginalConstructor()
                          ->setMethods(['get'])
                          ->getMockForAbstractClass()
        ;
        $container->expects($this->once())
                  ->method('get')
                  ->with('doctrine')
                  ->will($this->returnValue($doctrine))
        ;

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getContainer', 'getBundles'])
                       ->getMockForAbstractClass()
        ;
        $kernel->expects($this->once())
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;
        $kernel->expects($this->once())
               ->method('getBundles')
               ->will($this->returnValue([$bundle]))
        ;

        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClassMocked = new \ReflectionClass($webTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernelProperty->setValue($kernel);

        $reflectionMethod = $reflectionClass->getMethod('getMockedEntityWithId');
        $reflectionMethod->setAccessible(true);

        /** @var AnotherExampleEntity $mockedEntity */
        $mockedEntity = $reflectionMethod->invoke($webTestCase, 'TestingBundle:AnotherExampleEntity', 12345);

        $this->assertEquals(12345, $mockedEntity->getId());
        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\TestCase\AnotherExampleEntity', $mockedEntity);
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::getMockedEntityWithId
     */
    public function testGetMockedEntityWithId_Entity()
    {
        /** @type ClassMetadata $metadata */
        $metadata            = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
                                    ->disableOriginalConstructor()
                                    ->setMethods(['getAllMetadata'])
                                    ->getMock()
        ;
        $metadata->namespace = 'Cosma\Bundle\TestingBundle\Tests\TestCase';

        $metadataFactory = $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadataFactory')
                                ->disableOriginalConstructor()
                                ->setMethods(['getAllMetadata'])
                                ->getMock()
        ;
        $metadataFactory->expects($this->any())
                        ->method('getAllMetadata')
                        ->will($this->returnValue([$metadata]))
        ;

        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
                              ->disableOriginalConstructor()
                              ->setMethods(['getMetadataFactory'])
                              ->getMock()
        ;
        $entityManager->expects($this->any())
                      ->method('getMetadataFactory')
                      ->will($this->returnValue($metadataFactory))
        ;

        $bundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\BundleInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getName', 'getNamespace'])
                       ->getMockForAbstractClass()
        ;
        $bundle->expects($this->any())
               ->method('getName')
               ->will($this->returnValue('TestingBundle'))
        ;
        $bundle->expects($this->any())
               ->method('getNamespace')
               ->will($this->returnValue('Cosma\Bundle\TestingBundle\Tests\TestCase'))
        ;

        $doctrine = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')
                         ->disableOriginalConstructor()
                         ->setMethods(['getManager'])
                         ->getMock()
        ;
        $doctrine->expects($this->once())
                 ->method('getManager')
                 ->will($this->returnValue($entityManager))
        ;

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
                          ->disableOriginalConstructor()
                          ->setMethods(['get'])
                          ->getMockForAbstractClass()
        ;
        $container->expects($this->once())
                  ->method('get')
                  ->with('doctrine')
                  ->will($this->returnValue($doctrine))
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

        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClassMocked = new \ReflectionClass($webTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernelProperty->setValue($kernel);

        $currentBundleProperty = $reflectionClass->getProperty('currentBundle');
        $currentBundleProperty->setAccessible(true);
        $currentBundleProperty->setValue($bundle);

        $reflectionMethod = $reflectionClass->getMethod('getMockedEntityWithId');
        $reflectionMethod->setAccessible(true);

        /** @var SomeEntity $mockedEntity */
        $mockedEntity = $reflectionMethod->invoke($webTestCase, 'SomeEntity', 12345);

        $this->assertEquals(12345, $mockedEntity->getId());
        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\TestCase\SomeEntity', $mockedEntity);
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::getEntityWithId
     * @expectedException \Doctrine\ORM\EntityNotFoundException
     */
    public function testGetEntityWithId_Exception()
    {
        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClass  = new \ReflectionClass($webTestCase);
        $reflectionMethod = $reflectionClass->getMethod('getEntityWithId');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($webTestCase, 'Cosma\Bundle\TestingBundle\xxx', 12345);
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::getEntityWithId
     */
    public function testGetEntityWithId_FullNamespace()
    {
        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClass  = new \ReflectionClass($webTestCase);
        $reflectionMethod = $reflectionClass->getMethod('getEntityWithId');
        $reflectionMethod->setAccessible(true);

        /** @var ExampleEntity $entity */
        $entity = $reflectionMethod->invoke($webTestCase, 'Cosma\Bundle\TestingBundle\Tests\TestCase\SomeEntity', 12345);

        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\TestCase\SomeEntity', $entity);

        $this->assertEquals(12345, $entity->getId());
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::getEntityWithId
     */
    public function testGetEntityWithId_BundleAndEntity()
    {
        /** @type ClassMetadata $metadata */
        $metadata            = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
                                    ->disableOriginalConstructor()
                                    ->setMethods(['getAllMetadata'])
                                    ->getMock()
        ;
        $metadata->namespace = 'Cosma\Bundle\TestingBundle\Tests\TestCase';

        $metadataFactory = $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadataFactory')
                                ->disableOriginalConstructor()
                                ->setMethods(['getAllMetadata'])
                                ->getMock()
        ;
        $metadataFactory->expects($this->any())
                        ->method('getAllMetadata')
                        ->will($this->returnValue([$metadata]))
        ;

        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
                              ->disableOriginalConstructor()
                              ->setMethods(['getMetadataFactory'])
                              ->getMock()
        ;
        $entityManager->expects($this->any())
                      ->method('getMetadataFactory')
                      ->will($this->returnValue($metadataFactory))
        ;

        $bundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\BundleInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getName', 'getNamespace'])
                       ->getMockForAbstractClass()
        ;
        $bundle->expects($this->any())
               ->method('getName')
               ->will($this->returnValue('TestingBundle'))
        ;
        $bundle->expects($this->any())
               ->method('getNamespace')
               ->will($this->returnValue('Cosma\Bundle\TestingBundle\Tests\TestCase'))
        ;

        $doctrine = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')
                         ->disableOriginalConstructor()
                         ->setMethods(['getManager'])
                         ->getMock()
        ;
        $doctrine->expects($this->once())
                 ->method('getManager')
                 ->will($this->returnValue($entityManager))
        ;

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
                          ->disableOriginalConstructor()
                          ->setMethods(['get'])
                          ->getMockForAbstractClass()
        ;
        $container->expects($this->once())
                  ->method('get')
                  ->with('doctrine')
                  ->will($this->returnValue($doctrine))
        ;

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getContainer', 'getBundles'])
                       ->getMockForAbstractClass()
        ;
        $kernel->expects($this->once())
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;
        $kernel->expects($this->once())
               ->method('getBundles')
               ->will($this->returnValue([$bundle]))
        ;

        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClassMocked = new \ReflectionClass($webTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernelProperty->setValue($kernel);

        $reflectionMethod = $reflectionClass->getMethod('getEntityWithId');
        $reflectionMethod->setAccessible(true);

        /** @var AnotherExampleEntity $entity */
        $entity = $reflectionMethod->invoke($webTestCase, 'TestingBundle:AnotherExampleEntity', 12345);

        $this->assertEquals(12345, $entity->getId());
        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\TestCase\AnotherExampleEntity', $entity);
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::getEntityWithId
     */
    public function testGetEntityWithId_Entity()
    {
        /** @type ClassMetadata $metadata */
        $metadata            = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
                                    ->disableOriginalConstructor()
                                    ->setMethods(['getAllMetadata'])
                                    ->getMock()
        ;
        $metadata->namespace = 'Cosma\Bundle\TestingBundle\Tests\TestCase';

        $metadataFactory = $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadataFactory')
                                ->disableOriginalConstructor()
                                ->setMethods(['getAllMetadata'])
                                ->getMock()
        ;
        $metadataFactory->expects($this->any())
                        ->method('getAllMetadata')
                        ->will($this->returnValue([$metadata]))
        ;

        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
                              ->disableOriginalConstructor()
                              ->setMethods(['getMetadataFactory'])
                              ->getMock()
        ;
        $entityManager->expects($this->any())
                      ->method('getMetadataFactory')
                      ->will($this->returnValue($metadataFactory))
        ;

        $bundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\BundleInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getName', 'getNamespace'])
                       ->getMockForAbstractClass()
        ;
        $bundle->expects($this->any())
               ->method('getName')
               ->will($this->returnValue('TestingBundle'))
        ;
        $bundle->expects($this->any())
               ->method('getNamespace')
               ->will($this->returnValue('Cosma\Bundle\TestingBundle\Tests\TestCase'))
        ;

        $doctrine = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')
                         ->disableOriginalConstructor()
                         ->setMethods(['getManager'])
                         ->getMock()
        ;
        $doctrine->expects($this->once())
                 ->method('getManager')
                 ->will($this->returnValue($entityManager))
        ;

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
                          ->disableOriginalConstructor()
                          ->setMethods(['get'])
                          ->getMockForAbstractClass()
        ;
        $container->expects($this->once())
                  ->method('get')
                  ->with('doctrine')
                  ->will($this->returnValue($doctrine))
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

        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClassMocked = new \ReflectionClass($webTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernelProperty->setValue($kernel);

        $currentBundleProperty = $reflectionClass->getProperty('currentBundle');
        $currentBundleProperty->setAccessible(true);
        $currentBundleProperty->setValue($bundle);

        $reflectionMethod = $reflectionClass->getMethod('getEntityWithId');
        $reflectionMethod->setAccessible(true);

        /** @var SomeEntity $entity */
        $entity = $reflectionMethod->invoke($webTestCase, 'SomeEntity', 12345);

        $this->assertEquals(12345, $entity->getId());
        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\TestCase\SomeEntity', $entity);
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::loadTableFixtures
     *
     * @expectedException InvalidArgumentException
     */
    public function testLoadTableFixtures_Exception()
    {
        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClass = new \ReflectionClass($webTestCase);
        $method          = $reflectionClass->getParentClass()->getMethod('loadTableFixtures');
        $method->setAccessible(true);

        $method->invoke($webTestCase, []);
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::loadTableFixtures
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

        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClass = new \ReflectionClass($webTestCase);

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernelProperty->setValue($kernel);

        $currentBundleProperty = $reflectionClass->getParentClass()->getProperty('currentBundle');
        $currentBundleProperty->setAccessible(true);
        $currentBundleProperty->setValue($currentBundle);

        $loadTableFixturesMethod = $reflectionClass->getParentClass()->getMethod('loadTableFixtures');
        $loadTableFixturesMethod->setAccessible(true);

        $entities = $loadTableFixturesMethod->invoke($webTestCase, ['SomeEntity', 'AnotherExampleEntity']);

        $reflectionMethod = $reflectionClass->getMethod('tearDownAfterClass');
        $reflectionMethod->invoke(null);

        $this->assertEquals($this->getEntities(), $entities, 'Entities are wrong');
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::loadTestFixtures
     *
     * @expectedException InvalidArgumentException
     */
    public function testLoadTestFixtures_Exception()
    {
        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClass = new \ReflectionClass($webTestCase);
        $method          = $reflectionClass->getParentClass()->getMethod('loadTestFixtures');
        $method->setAccessible(true);

        $method->invoke($webTestCase, []);
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::loadTestFixtures
     */
    public function testLoadTestFixtures()
    {
        $fixtureManager = $this->getMockBuilder('h4cc\AliceFixturesBundle\Fixtures\FixtureManagerInterface')
                               ->disableOriginalConstructor()
                               ->setMethods(['persist', 'loadFiles'])
                               ->getMockForAbstractClass()
        ;
        $fixtureManager->expects($this->once())
                       ->method('loadFiles')
                       ->with([
                                  'Cosma/Bundle/TestingBundle/FixtureDirectory/Test/TestCase/WebTestCase/SomeEntity.yml',
                                  'Cosma/Bundle/TestingBundle/FixtureDirectory/Test/TestCase/WebTestCase/AnotherExampleEntity.yml'
                              ])
                       ->will($this->returnValue($this->getEntities()))
        ;
        $fixtureManager->expects($this->atLeast(2))
                       ->method('persist')
                       ->will($this->returnValue(true))
        ;

        $valueMap  = [
            ['cosma_testing.fixture_directory', 'FixtureDirectory'],
            ['cosma_testing.tests_directory', 'Tests']
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

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getContainer', 'getBundles'])
                       ->getMockForAbstractClass()
        ;
        $kernel->expects($this->atLeast(3))
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;

        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
                            ->disableOriginalConstructor()
                            ->setMethods(['getTestClassPath'])
                            ->getMockForAbstractClass()
        ;
        $webTestCase->expects($this->once())
                    ->method('getTestClassPath')
                    ->will($this->returnValue('TestCase/WebTestCase'))
        ;

        $reflectionClass = new \ReflectionClass($webTestCase);

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernelProperty->setValue($kernel);

        $currentBundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\BundleInterface')
                              ->disableOriginalConstructor()
                              ->setMethods(['getPath'])
                              ->getMockForAbstractClass()
        ;
        $currentBundle->expects($this->once())
                      ->method('getPath')
                      ->will($this->returnValue('Cosma/Bundle/TestingBundle'))
        ;

        $currentBundleProperty = $reflectionClass->getParentClass()->getProperty('currentBundle');
        $currentBundleProperty->setAccessible(true);
        $currentBundleProperty->setValue($currentBundle);

        $loadTestFixturesMethod = $reflectionClass->getParentClass()->getMethod('loadTestFixtures');
        $loadTestFixturesMethod->setAccessible(true);

        $entities = $loadTestFixturesMethod->invoke($webTestCase, ['SomeEntity', 'AnotherExampleEntity']);

        $reflectionMethod = $reflectionClass->getMethod('tearDownAfterClass');
        $reflectionMethod->invoke(null);

        $this->assertEquals($this->getEntities(), $entities, 'Entities are wrong');
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::loadCustomFixtures
     *
     * @expectedException InvalidArgumentException
     */
    public function testLoadCustomFixtures_Exception()
    {
        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClass = new \ReflectionClass($webTestCase);
        $method          = $reflectionClass->getParentClass()->getMethod('loadCustomFixtures');
        $method->setAccessible(true);

        $method->invoke($webTestCase, []);
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::loadCustomFixtures
     */
    public function testLoadCustomFixtures()
    {
        $fixtureManager = $this->getMockBuilder('h4cc\AliceFixturesBundle\Fixtures\FixtureManagerInterface')
                               ->disableOriginalConstructor()
                               ->setMethods(['persist', 'loadFiles'])
                               ->getMockForAbstractClass()
        ;
        $fixtureManager->expects($this->once())
                       ->method('loadFiles')
                       ->with([
                                  'Cosma/Bundle/TestingBundle/Some/Custom/Path/Directory/SomeEntity.yml',
                                  'Cosma/Bundle/TestingBundle/Some/Custom/Path/Directory/AnotherExampleEntity.yml'
                              ])
                       ->will($this->returnValue($this->getEntities()))
        ;
        $fixtureManager->expects($this->atLeast(2))
                       ->method('persist')
                       ->will($this->returnValue(true))
        ;

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
                          ->disableOriginalConstructor()
                          ->setMethods(['get', 'getParameter'])
                          ->getMockForAbstractClass()
        ;
        $container->expects($this->once())
                  ->method('get')
                  ->with('h4cc_alice_fixtures.manager')
                  ->will($this->returnValue($fixtureManager))
        ;

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getContainer', 'getBundles'])
                       ->getMockForAbstractClass()
        ;
        $kernel->expects($this->once())
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;

        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClass = new \ReflectionClass($webTestCase);

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernelProperty->setValue($kernel);

        $currentBundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\BundleInterface')
                              ->disableOriginalConstructor()
                              ->setMethods(['getPath'])
                              ->getMockForAbstractClass()
        ;
        $currentBundle->expects($this->any())
                      ->method('getPath')
                      ->will($this->returnValue('Cosma/Bundle/TestingBundle'))
        ;

        $currentBundleProperty = $reflectionClass->getParentClass()->getProperty('currentBundle');
        $currentBundleProperty->setAccessible(true);
        $currentBundleProperty->setValue($currentBundle);

        $loadTestFixturesMethod = $reflectionClass->getParentClass()->getMethod('loadCustomFixtures');
        $loadTestFixturesMethod->setAccessible(true);

        $entities = $loadTestFixturesMethod->invoke(
            $webTestCase,
            [
                'Some/Custom/Path/Directory/SomeEntity',
                'Some/Custom/Path/Directory/AnotherExampleEntity'
            ]
        );

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


