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

use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Client;

use Cosma\Bundle\TestingBundle\TestCase\WebTestCase;

class WebTestCaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase
     */
    public function testStaticAttributes()
    {
        $this->assertClassHasStaticAttribute('currentBundle', 'Cosma\Bundle\TestingBundle\TestCase\WebTestCase');
        $this->assertClassHasStaticAttribute('fixtureManager', 'Cosma\Bundle\TestingBundle\TestCase\WebTestCase');
        $this->assertClassHasStaticAttribute('fixturePath', 'Cosma\Bundle\TestingBundle\TestCase\WebTestCase');
        $this->assertClassHasStaticAttribute('entityNameSpace', 'Cosma\Bundle\TestingBundle\TestCase\WebTestCase');
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::tearDownAfterClass
     */
    public function testTearDownAfterClass()
    {
        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
            ->disableOriginalConstructor()
            ->getMock();

        $reflectionClassMocked = new \ReflectionClass($webTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $currentBundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\BundleInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $currentBundleProperty = $reflectionClass->getProperty('currentBundle');
        $currentBundleProperty->setAccessible(true);
        $currentBundleProperty->setValue($currentBundle);

        $fixtureManager = $this->getMockBuilder('h4cc\AliceFixturesBundle\Fixtures\FixtureManager')
            ->disableOriginalConstructor()
            ->getMock();

        $fixtureManagerProperty = $reflectionClass->getProperty('fixtureManager');
        $fixtureManagerProperty->setAccessible(true);
        $fixtureManagerProperty->setValue($fixtureManager);

        $fixturePathProperty = $reflectionClass->getProperty('fixturePath');
        $fixturePathProperty->setAccessible(true);
        $fixturePathProperty->setValue('fixture/path');

        $entityNameSpaceProperty = $reflectionClass->getProperty('entityNameSpace');
        $entityNameSpaceProperty->setAccessible(true);
        $entityNameSpaceProperty->setValue('Cosma\Some\Namespace');

        $reflectionMethod = $reflectionClass->getMethod('tearDownAfterClass');
        $reflectionMethod->invoke(null);

        $this->assertNull($currentBundleProperty->getValue($webTestCase));
        $this->assertNull($fixtureManagerProperty->getValue($webTestCase));
        $this->assertNull($fixturePathProperty->getValue($webTestCase));
        $this->assertNull($entityNameSpaceProperty->getValue($webTestCase));
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::getClient
     */
    public function testGetClient()
    {
        $client = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Client')
            ->disableOriginalConstructor()
            ->getMock();

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();
        $container->expects($this->once())
            ->method('get')
            ->with('test.client')
            ->will($this->returnValue($client));

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getContainer'))
            ->getMockForAbstractClass();
        $kernel->expects($this->once())
            ->method('getContainer')
            ->will($this->returnValue($container));

        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

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
            ->setMethods(array())
            ->getMock();

        $doctrine = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->setMethods(array('getManager'))
            ->getMock();
        $doctrine->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($entityManager));

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();
        $container->expects($this->once())
            ->method('get')
            ->with('doctrine')
            ->will($this->returnValue($doctrine));

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getContainer'))
            ->getMockForAbstractClass();
        $kernel->expects($this->once())
            ->method('getContainer')
            ->will($this->returnValue($container));

        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

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
            ->getMock();

        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getRepository'))
            ->getMock();
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with('BundleExample:User')
            ->will($this->returnValue($entityRepository));


        $doctrine = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->setMethods(array('getManager'))
            ->getMock();
        $doctrine->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($entityManager));

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();
        $container->expects($this->once())
            ->method('get')
            ->with('doctrine')
            ->will($this->returnValue($doctrine));

        $currentBundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\BundleInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getNamespace', 'getName'))
            ->getMockForAbstractClass();
        $currentBundle->expects($this->once())
            ->method('getNamespace')
            ->will($this->returnValue('Mock_WebTestCase'));
        $currentBundle->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('BundleExample'));

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getContainer', 'getBundles'))
            ->getMockForAbstractClass();
        $kernel->expects($this->once())
            ->method('getContainer')
            ->will($this->returnValue($container));
        $kernel->expects($this->once())
            ->method('getBundles')
            ->will($this->returnValue(array($currentBundle)));



        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();


        $reflectionClass = new \ReflectionClass($webTestCase);

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernelProperty->setValue($kernel);

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
            ->getMockForAbstractClass();

        $reflectionClass = new \ReflectionClass($webTestCase);
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
            ->getMockForAbstractClass();

        $reflectionClass = new \ReflectionClass($webTestCase);
        $reflectionMethod = $reflectionClass->getMethod('getMockedEntityWithId');
        $reflectionMethod->setAccessible(true);

        /** @var ExampleEntity $mockedEntity */
        $mockedEntity = $reflectionMethod->invoke($webTestCase, 'Cosma\Bundle\TestingBundle\Tests\TestCase\SomeEntity', 12345);

        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\TestCase\SomeEntity', $mockedEntity);
        $this->assertEquals(12345, $mockedEntity->getId());
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::getMockedEntityWithId
     */
    public function testGetMockedEntityWithId_NoNamespace()
    {
        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $reflectionClassMocked = new \ReflectionClass($webTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $entityNameSpaceProperty = $reflectionClass->getProperty('entityNameSpace');
        $entityNameSpaceProperty->setAccessible(true);
        $entityNameSpaceProperty->setValue('Cosma\Bundle\TestingBundle\Tests\TestCase');

        $reflectionMethod = $reflectionClass->getMethod('getMockedEntityWithId');
        $reflectionMethod->setAccessible(true);

        /** @var AnotherExampleEntity $mockedEntity */
        $mockedEntity = $reflectionMethod->invoke($webTestCase, 'AnotherExampleEntity', 12345);

        $this->assertEquals(12345, $mockedEntity->getId());
        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\TestCase\AnotherExampleEntity', $mockedEntity);
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::getEntityWithId
     * @expectedException \Doctrine\ORM\EntityNotFoundException
     */
    public function testGetEntityWithId_Exception()
    {
        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $reflectionClass = new \ReflectionClass($webTestCase);
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
            ->getMockForAbstractClass();


        $reflectionClass = new \ReflectionClass($webTestCase);
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
    public function testGetEntityWithId_NoNamespace()
    {
        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $reflectionClassMocked = new \ReflectionClass($webTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $entityNameSpaceProperty = $reflectionClass->getProperty('entityNameSpace');
        $entityNameSpaceProperty->setAccessible(true);
        $entityNameSpaceProperty->setValue('Cosma\Bundle\TestingBundle\Tests\TestCase');

        $reflectionMethod = $reflectionClass->getMethod('getEntityWithId');
        $reflectionMethod->setAccessible(true);

        /** @var AnotherExampleEntity $entity */
        $entity = $reflectionMethod->invoke($webTestCase, 'AnotherExampleEntity', 12345);

        $this->assertEquals(12345, $entity->getId());
        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\TestCase\AnotherExampleEntity', $entity);
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
            ->getMockForAbstractClass();

        $reflectionClass = new \ReflectionClass($webTestCase);
        $method          = $reflectionClass->getParentClass()->getMethod('loadTableFixtures');
        $method->setAccessible(true);

        $method->invoke($webTestCase, array());
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::loadTableFixtures
     */
    public function testLoadTableFixtures()
    {
        $fixtureManager = $this->getMockBuilder('h4cc\AliceFixturesBundle\Fixtures\FixtureManagerInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('persist', 'loadFiles'))
            ->getMockForAbstractClass();
        $fixtureManager->expects($this->any())
            ->method('persist')
            ->with()
            ->will($this->returnValue(true));
        $fixtureManager->expects($this->any())
            ->method('loadFiles')
            ->with(array(
                'Cosma/Bundle/TestingBundle/FixtureDirectory/Table/SomeEntity.yml',
                'Cosma/Bundle/TestingBundle/FixtureDirectory/Table/AnotherExampleEntity.yml'
            ))
            ->will($this->returnValue($this->getEntities()));

        $valueMap = array(
            array('testing_cosma.fixture_path', 'FixtureDirectory'),
            array('testing_cosma.fixture_table_directory', 'Table')
        );
        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('get', 'getParameter'))
            ->getMockForAbstractClass();
        $container->expects($this->any())
            ->method('getParameter')
            ->will($this->returnValueMap($valueMap));
        $container->expects($this->once())
            ->method('get')
            ->with('h4cc_alice_fixtures.manager')
            ->will($this->returnValue($fixtureManager));

        $currentBundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\BundleInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getPath'))
            ->getMockForAbstractClass();
        $currentBundle->expects($this->once())
            ->method('getPath')
            ->will($this->returnValue('Cosma/Bundle/TestingBundle'));

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getContainer', 'getBundles'))
            ->getMockForAbstractClass();
        $kernel->expects($this->atLeast(3))
            ->method('getContainer')
            ->will($this->returnValue($container));

        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $reflectionClass = new \ReflectionClass($webTestCase);

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernelProperty->setValue($kernel);

        $currentBundleProperty = $reflectionClass->getParentClass()->getProperty('currentBundle');
        $currentBundleProperty->setAccessible(true);
        $currentBundleProperty->setValue($currentBundle);

        $loadTableFixturesMethod          = $reflectionClass->getParentClass()->getMethod('loadTableFixtures');
        $loadTableFixturesMethod->setAccessible(true);

        $entities = $loadTableFixturesMethod->invoke($webTestCase, array('SomeEntity', 'AnotherExampleEntity'));

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
            ->getMockForAbstractClass();

        $reflectionClass = new \ReflectionClass($webTestCase);
        $method          = $reflectionClass->getParentClass()->getMethod('loadTestFixtures');
        $method->setAccessible(true);

        $method->invoke($webTestCase, array());
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
            ->getMockForAbstractClass();

        $reflectionClass = new \ReflectionClass($webTestCase);
        $method          = $reflectionClass->getParentClass()->getMethod('loadCustomFixtures');
        $method->setAccessible(true);

        $method->invoke($webTestCase, array());
    }


    /**
     * @return array
     */
    private function getEntities()
    {
        $objects   = array();
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



class WebTestCaseExample extends WebTestCase
{
}

class SomeEntity
{
    private $id;

    private $name;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}

class AnotherExampleEntity
{
    private $id;

    private $firstName;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $name
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }
}


