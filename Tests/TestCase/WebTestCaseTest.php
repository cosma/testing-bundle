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

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Client;

use Cosma\Bundle\TestingBundle\TestCase\WebTestCase;
use h4cc\AliceFixturesBundle\Fixtures\FixtureManager as AliceFixtureManager;

class WebTestCaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase
     */
    public function testStaticAttributes()
    {
        $this->assertClassHasStaticAttribute('client', 'Cosma\Bundle\TestingBundle\TestCase\WebTestCase');
        $this->assertClassHasStaticAttribute('fixturePath', 'Cosma\Bundle\TestingBundle\TestCase\WebTestCase');
        $this->assertClassHasStaticAttribute('entityNameSpace', 'Cosma\Bundle\TestingBundle\TestCase\WebTestCase');
        $this->assertClassHasStaticAttribute('currentBundle', 'Cosma\Bundle\TestingBundle\TestCase\WebTestCase');
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::getClient
     */
    public function testGetClient()
    {
        $webTestCase = $this->getMockedWebTestCase();

        $reflectionClass = new \ReflectionClass($webTestCase);

        $reflectionMethod = $reflectionClass->getMethod('getClient');
        $reflectionMethod->setAccessible(true);

        /** @var Client $actual */
        $client = $reflectionMethod->invoke($webTestCase);

        $this->assertInstanceOf(
            'Symfony\Bundle\FrameworkBundle\Client',
            $client,
            'must return a Client object'
        );
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::getContainer
     */
    public function testGetContainer()
    {
        $webTestCase = $this->getMockedWebTestCase();

        $reflectionClass = new \ReflectionClass($webTestCase);

        $reflectionMethod = $reflectionClass->getMethod('getContainer');
        $reflectionMethod->setAccessible(true);

        /** @var ContainerInterface $actual */
        $container = $reflectionMethod->invoke($webTestCase);

        $this->assertInstanceOf(
            'Symfony\Component\DependencyInjection\ContainerInterface',
            $container,
            'must return a ContainerInterface object'
        );
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::getEntityManager
     */
    public function testGetEntityManager()
    {
        $webTestCase = $this->getMockedWebTestCase();

        $reflectionClass = new \ReflectionClass($webTestCase);

        $reflectionMethod = $reflectionClass->getMethod('getEntityManager');
        $reflectionMethod->setAccessible(true);

        /** @var EntityManager $actual */
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
        $webTestCase = $this->getMockedWebTestCase();

        $reflectionClass = new \ReflectionClass($webTestCase);

        $reflectionMethod = $reflectionClass->getMethod('getEntityRepository');
        $reflectionMethod->setAccessible(true);

        /** @var EntityRepository $actual */
        $entityRepository = $reflectionMethod->invoke($webTestCase, 'SomeEntity');

        $this->assertInstanceOf(
            'Doctrine\ORM\EntityRepository',
            $entityRepository,
            'must return a EntityRepository object'
        );
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::getMockedEntityWithId
     */
    public function testGetMockedEntityWithId()
    {
        $webTestCase = $this->getMockedWebTestCase();

        $method = new \ReflectionMethod(
            'Cosma\Bundle\TestingBundle\TestCase\WebTestCase',
            'getMockedEntityWithId'
        );
        $method->setAccessible(true);

        /** @var SomeEntity $mockedEntity */
        $mockedEntity = $method->invoke($webTestCase, 'Cosma\Bundle\TestingBundle\Tests\TestCase\SomeEntity', 12345);

        $this->assertEquals(12345, $mockedEntity->getId());
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::getMockedEntityWithId
     * @expectedException \Doctrine\ORM\EntityNotFoundException
     */
    public function testGetMockedEntityWithId_Exception()
    {
        $webTestCase = $this->getMockedWebTestCase();

        $method = new \ReflectionMethod(
            'Cosma\Bundle\TestingBundle\TestCase\WebTestCase',
            'getMockedEntityWithId'
        );
        $method->setAccessible(true);

        $method->invoke($webTestCase, 'XX\XXXXX', 12345);
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::getEntityWithId
     */
    public function testGetEntityWithId()
    {
        $webTestCase = $this->getMockedWebTestCase();

        $method = new \ReflectionMethod(
            'Cosma\Bundle\TestingBundle\TestCase\WebTestCase',
            'getEntityWithId'
        );
        $method->setAccessible(true);

        /** @var SomeEntity $entity */
        $entity = $method->invoke($webTestCase, 'Cosma\Bundle\TestingBundle\Tests\TestCase\SomeEntity', 12345);

        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\TestCase\SomeEntity', $entity);
        $this->assertEquals(12345, $entity->getId());
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::getEntityWithId
     * @expectedException \Doctrine\ORM\EntityNotFoundException
     */
    public function testGetEntityWithId_Exception()
    {
        $webTestCase = $this->getMockedWebTestCase();

        $method = new \ReflectionMethod(
            'Cosma\Bundle\TestingBundle\TestCase\WebTestCase',
            'getEntityWithId'
        );
        $method->setAccessible(true);

        $method->invoke($webTestCase, 'XX\XXXXX', 12345);
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::loadTableFixtures
     *
     * @expectedException InvalidArgumentException
     */
    public function testLoadTableFixtures_Exception()
    {
        $webTestCase = $this->getMockedWebTestCaseWithTableFixture();

        $method = new \ReflectionMethod(
            'Cosma\Bundle\TestingBundle\TestCase\WebTestCase',
            'loadTableFixtures'
        );
        $method->setAccessible(true);

        $method->invoke($webTestCase, array());
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::loadTableFixtures
     */
    public function testLoadTableFixtures()
    {
        $webTestCase = $this->getMockedWebTestCaseWithTableFixture();

        $method = new \ReflectionMethod(
            'Cosma\Bundle\TestingBundle\TestCase\WebTestCase',
            'loadTableFixtures'
        );
        $method->setAccessible(true);

        $entities = $method->invoke($webTestCase, array('SomeEntity', 'AnotherExampleEntity'));

        $this->assertEquals($this->getEntities(), $entities, 'Entities are wrong');
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::loadTestFixtures
     *
     * @expectedException InvalidArgumentException
     */
    public function testLoadTestFixtures_Exception()
    {
        $webTestCase = $this->getMockedWebTestCaseWithTableFixture();

        $method = new \ReflectionMethod(
            'Cosma\Bundle\TestingBundle\TestCase\WebTestCase',
            'loadTestFixtures'
        );
        $method->setAccessible(true);

        $method->invoke($webTestCase, array());
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::loadTestFixtures
     */
    public function testLoadTestFixtures()
    {
        $webTestCase = $this->getMockedWebTestCaseWithTestFixture();

        $reflectionClass = new \ReflectionClass($webTestCase);

        $loadTestFixturesMethod = $reflectionClass->getMethod('loadTestFixtures');
        $loadTestFixturesMethod->setAccessible(true);

        $entities = $loadTestFixturesMethod->invoke($webTestCase, array('SomeTestEntity', 'AnotherTestEntity'));

        $this->assertEquals($this->getEntities(), $entities, 'Entities are wrong');
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::loadCustomFixtures
     *
     * @expectedException InvalidArgumentException
     */
    public function testLoadCustomFixtures_Exception()
    {
        $webTestCase = $this->getMockedWebTestCaseWithCustomFixture();

        $method = new \ReflectionMethod(
            'Cosma\Bundle\TestingBundle\TestCase\WebTestCase',
            'loadCustomFixtures'
        );
        $method->setAccessible(true);

        $method->invoke($webTestCase, array());
    }

    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::loadCustomFixtures
     */
    public function testLoadCustomFixtures()
    {
        $webTestCase = $this->getMockedWebTestCaseWithCustomFixture();

        $reflectionClass = new \ReflectionClass($webTestCase);

        $loadTestFixturesMethod = $reflectionClass->getMethod('loadCustomFixtures');
        $loadTestFixturesMethod->setAccessible(true);

        $entities = $loadTestFixturesMethod->invoke(
            $webTestCase,
            array(
                'Some/Custom/Path/Directory/SomeEntity.yml',
                'Some/Custom/Path/Directory/AnotherExampleEntity.yml'
            )
        );

        $this->assertEquals($this->getEntities(), $entities, 'Entities are wrong');
    }

    /**
     * @return WebTestCase
     */
    private function getMockedWebTestCase()
    {
        $entityRepository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableAutoload()
            ->getMock();

        $entityManager = $this->getEntityManager($entityRepository);

        $container = $this->getContainerWithEntityManager($entityManager);

        $client = $this->getClient($container);

        return $this->getDefaultMockedWebTestCase($client);
    }

    /**
     * @return WebTestCase
     */
    private function getMockedWebTestCaseWithTableFixture()
    {

        $aliceFixtureManager = $this->getAliceFixtureManagerForTable();

        $container = $this->getContainerWithFixtureManager($aliceFixtureManager);

        $client = $this->getClient($container);

        $webTestCaseMocked = $this->getDefaultMockedWebTestCase($client);

        return $webTestCaseMocked;
    }

    /**
     * @return WebTestCase
     */
    private function getMockedWebTestCaseWithTestFixture()
    {

        $aliceFixtureManager = $this->getAliceFixtureManagerForTest();

        $container = $this->getContainerWithFixtureManager($aliceFixtureManager);

        $client = $this->getClient($container);

        $webTestCaseMocked = $this->getDefaultMockedWebTestCase($client);

        return $webTestCaseMocked;
    }

    /**
     * @return WebTestCase
     */
    private function getMockedWebTestCaseWithCustomFixture()
    {

        $aliceFixtureManager = $this->getAliceFixtureManagerForCustom();

        $container = $this->getContainerWithFixtureManager($aliceFixtureManager);

        $client = $this->getClient($container);

        $webTestCaseMocked = $this->getDefaultMockedWebTestCase($client);

        return $webTestCaseMocked;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getCurrentBundle()
    {
        $currentBundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\BundleInterface')
            ->disableAutoload()
            ->setMethods(array('getName', 'getNameSpace', 'getPath'))
            ->getMock();
        $currentBundle->expects($this->any())
            ->method('getName')
            ->with()
            ->will($this->returnValue('BundleName'));
        $currentBundle->expects($this->any())
            ->method('getNameSpace')
            ->with()
            ->will($this->returnValue('Bundles\BundleName'));
        $currentBundle->expects($this->any())
            ->method('getPath')
            ->with()
            ->will($this->returnValue('Cosma/Bundle/TestingBundle'));

        return $currentBundle;
    }

    /**
     * @param EntityRepository $entityRepository
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getEntityManager(EntityRepository $entityRepository)
    {
        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableAutoload()
            ->setMethods(array('getRepository'))
            ->getMock();
        $entityManager->expects($this->any())
            ->method('getRepository')
            ->with('BundleName:SomeEntity')
            ->will($this->returnValue($entityRepository));

        return $entityManager;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getAliceFixtureManagerForTable()
    {
        $entities = $this->getEntities();

        $aliceFixtureManager = $this->getMockBuilder('h4cc\AliceFixturesBundle\Fixtures\FixtureManager')
            ->disableAutoload()
            ->setMethods(array('persist', 'loadFiles'))
            ->getMock();
        $aliceFixtureManager->expects($this->any())
            ->method('persist')
            ->with()
            ->will($this->returnValue(true));
        $aliceFixtureManager->expects($this->any())
            ->method('loadFiles')
            ->with(array(
                'Cosma/Bundle/TestingBundle/FixtureDirectory/Table/SomeEntity.yml',
                'Cosma/Bundle/TestingBundle/FixtureDirectory/Table/AnotherExampleEntity.yml'
            ))
            ->will($this->returnValue($entities));

        return $aliceFixtureManager;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getAliceFixtureManagerForTest()
    {
        $entities = $this->getEntities();

        $aliceFixtureManager = $this->getMockBuilder('h4cc\AliceFixturesBundle\Fixtures\FixtureManager')
            ->disableAutoload()
            ->setMethods(array('persist', 'loadFiles'))
            ->getMock();
        $aliceFixtureManager->expects($this->any())
            ->method('persist')
            ->with()
            ->will($this->returnValue(true));
        $aliceFixtureManager->expects($this->any())
            ->method('loadFiles')
            ->with(array(
                'Cosma/Bundle/TestingBundle/FixtureDirectory/Test/TestCase/WebTestCase/SomeTestEntity.yml',
                'Cosma/Bundle/TestingBundle/FixtureDirectory/Test/TestCase/WebTestCase/AnotherTestEntity.yml'
            ))
            ->will($this->returnValue($entities));

        return $aliceFixtureManager;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getAliceFixtureManagerForCustom()
    {
        $entities = $this->getEntities();

        $aliceFixtureManager = $this->getMockBuilder('h4cc\AliceFixturesBundle\Fixtures\FixtureManager')
            ->disableAutoload()
            ->setMethods(array('persist', 'loadFiles'))
            ->getMock();
        $aliceFixtureManager->expects($this->any())
            ->method('persist')
            ->with()
            ->will($this->returnValue(true));
        $aliceFixtureManager->expects($this->any())
            ->method('loadFiles')
            ->with(array(
                'Cosma/Bundle/TestingBundle/Some/Custom/Path/Directory/SomeEntity.yml',
                'Cosma/Bundle/TestingBundle/Some/Custom/Path/Directory/AnotherExampleEntity.yml'
            ))
            ->will($this->returnValue($entities));

        return $aliceFixtureManager;
    }

    /**
     * @param EntityManager $entityManager
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getContainerWithEntityManager(EntityManager $entityManager)
    {
        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableAutoload()
            ->setMethods(array('get', 'hasParameter', 'getParameter'))
            ->getMock();
        $container->expects($this->any())
            ->method('hasParameter')
            ->with('fixture_path')
            ->will($this->returnValue(true));
        $container->expects($this->any())
            ->method('getParameter')
            ->with('fixture_path')
            ->will($this->returnValue('FixtureDirectory'));
        $container->expects($this->any())
            ->method('get')
            ->with('doctrine.orm.entity_manager')
            ->will($this->returnValue($entityManager));


        return $container;
    }

    /**
     * @param AliceFixtureManager $aliceFixtureManager
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getContainerWithFixtureManager(AliceFixtureManager $aliceFixtureManager)
    {
        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableAutoload()
            ->setMethods(array('get', 'hasParameter', 'getParameter'))
            ->getMock();
        $container->expects($this->any())
            ->method('hasParameter')
            ->with('fixture_path')
            ->will($this->returnValue(true));
        $container->expects($this->any())
            ->method('getParameter')
            ->with('fixture_path')
            ->will($this->returnValue('FixtureDirectory'));
        $container->expects($this->any())
            ->method('get')
            ->with('h4cc_alice_fixtures.manager')
            ->will($this->returnValue($aliceFixtureManager));

        return $container;
    }

    /**
     * @param ContainerInterface $container
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getClient(ContainerInterface $container)
    {
        $client = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Client')
            ->disableAutoload()
            ->setMethods(array('getContainer'))
            ->getMock();
        $client->expects($this->any())
            ->method('getContainer')
            ->with()
            ->will($this->returnValue($container));

        return $client;
    }

    /**
     * @param Client $client
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getDefaultMockedWebTestCase(Client $client)
    {
        $webTestCaseMocked = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
            ->disableAutoload()
            ->setMethods(array('setUpBeforeClass', 'getTestClassPath'))
            ->getMockForAbstractClass();
        $webTestCaseMocked->expects($this->any())
            ->method('getTestClassPath')
            ->with()
            ->will($this->returnValue('TestCase/WebTestCase'));

        $reflectionClassMocked = new \ReflectionClass($webTestCaseMocked);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $clientProperty = $reflectionClass->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($webTestCaseMocked, $client);

        $currentBundle         = $this->getCurrentBundle();
        $currentBundleProperty = $reflectionClass->getProperty('currentBundle');
        $currentBundleProperty->setAccessible(true);
        $currentBundleProperty->setValue($webTestCaseMocked, $currentBundle);

        $entityNameSpaceProperty = $reflectionClass->getProperty('entityNameSpace');
        $entityNameSpaceProperty->setAccessible(true);
        $entityNameSpaceProperty->setValue($webTestCaseMocked, 'Cosma\Bundle\TestingBundle\Entity');

        return $webTestCaseMocked;
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



