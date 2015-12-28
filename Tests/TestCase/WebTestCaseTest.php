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

class WebTestCaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::setUp
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
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::getKernel
     */
    public function testGetKernel()
    {
        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClass = new \ReflectionClass($webTestCase);

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernelProperty->setValue(null);

        $reflectionMethod = $reflectionClass->getMethod('getKernel');
        $reflectionMethod->setAccessible(true);

        $kernel = $reflectionMethod->invoke($webTestCase);

        $this->assertInstanceOf(
            'Symfony\Component\HttpKernel\HttpKernelInterface',
            $kernel,
            'must return a kernel object'
        );
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::getContainer
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

        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
                            ->disableOriginalConstructor()
                            ->getMockForAbstractClass()
        ;

        $reflectionClass = new \ReflectionClass($webTestCase);

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernelProperty->setValue($kernel);

        $reflectionMethod = $reflectionClass->getMethod('getContainer');
        $reflectionMethod->setAccessible(true);

        $container = $reflectionMethod->invoke($webTestCase);

        $this->assertInstanceOf(
            'Symfony\Component\DependencyInjection\ContainerInterface',
            $container,
            'must return a Container object'
        );
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

        $client = $reflectionMethod->invoke($webTestCase);

        $this->assertInstanceOf(
            'Symfony\Bundle\FrameworkBundle\Client',
            $client,
            'must return a Client object'
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
            'Cosma\Bundle\TestingBundle\Tests\SomeEntity',
            12345
        );

        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\SomeEntity', $mockedEntity);
        $this->assertEquals(12345, $mockedEntity->getId());
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::getMockedEntityWithId
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

        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
                            ->disableOriginalConstructor()
                            ->setMethods(['getEntityManager'])
                            ->getMockForAbstractClass()
        ;

        $webTestCase->expects($this->once())
                    ->method('getEntityManager')
                    ->will($this->returnValue($entityManager))
        ;

        $reflectionClassMocked = new \ReflectionClass($webTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $reflectionMethod = $reflectionClass->getMethod('getMockedEntityWithId');
        $reflectionMethod->setAccessible(true);

        /** @var AnotherExampleEntity $mockedEntity */
        $mockedEntity = $reflectionMethod->invoke($webTestCase, 'TestingBundle:AnotherExampleEntity', 4567);

        $this->assertEquals(4567, $mockedEntity->getId());
        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\AnotherExampleEntity', $mockedEntity);
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
        $entity = $reflectionMethod->invoke($webTestCase, 'Cosma\Bundle\TestingBundle\Tests\SomeEntity', 12345);

        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\SomeEntity', $entity);

        $this->assertEquals(12345, $entity->getId());
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::getEntityWithId
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

        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
                            ->disableOriginalConstructor()
                            ->setMethods(['getEntityManager'])
                            ->getMockForAbstractClass()
        ;

        $webTestCase->expects($this->once())
                    ->method('getEntityManager')
                    ->will($this->returnValue($entityManager))
        ;

        $reflectionClassMocked = new \ReflectionClass($webTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $reflectionMethod = $reflectionClass->getMethod('getEntityWithId');
        $reflectionMethod->setAccessible(true);

        /** @var AnotherExampleEntity $entity */
        $entity = $reflectionMethod->invoke($webTestCase, 'TestingBundle:AnotherExampleEntity', 12345);

        $this->assertEquals(12345, $entity->getId());
        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\AnotherExampleEntity', $entity);
    }
}

