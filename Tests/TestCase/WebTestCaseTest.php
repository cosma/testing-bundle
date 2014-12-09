<?php
namespace Cosma\Bundle\TestingBundle\Tests\TestCase;

use Cosma\Bundle\TestingBundle\DependencyInjection\TestingExtension;
use Cosma\Bundle\TestingBundle\TestCase\WebTestCase;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

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
        $entityRepository = $reflectionMethod->invoke($webTestCase, 'ExampleEntity');

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

        /** @var ExampleEntity $mockedEntity */
        $mockedEntity = $method->invoke($webTestCase, 'Cosma\Bundle\TestingBundle\Tests\TestCase\ExampleEntity', 12345);

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

        /** @var ExampleEntity $entity */
        $entity = $method->invoke($webTestCase, 'Cosma\Bundle\TestingBundle\Tests\TestCase\ExampleEntity', 12345);

        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\TestCase\ExampleEntity', $entity);
        $this->assertEquals(12345, $entity->getId());
    }

    /**
     * @see WebTestCase::getEntityWithId
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
        $method->returnsReference();

        $method->invoke($webTestCase, 'XX\XXXXX', 12345);
    }

    /**
     * @see WebTestCase::getEntityWithId
     * @expectedException \Doctrine\ORM\EntityNotFoundException
     */
    public function testLoadTableFixtures()
    {
        $entityOne = new ExampleEntity();
        $entityOne->setName('One');

        $entityTwo = new ExampleEntity();
        $entityTwo->setName('Two');

        $objects = new ArrayCollection();

        $objects->add($entityOne);
        $objects->add($entityTwo);

        $webTestCaseMocked = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
            ->disableOriginalConstructor()
            ->setMethods(array('appendTableFixturesPath', 'loadFixture'))
            ->getMockForAbstractClass();

        $webTestCaseMocked->expects($this->once())
            ->method('appendTableFixturesPath')
            ->with(array('User', 'Group'))
            ->will($this->returnValue(array('src/Cosma/Fixture/Table/User.yml', 'src/Cosma/Fixture/Table/Group.yml')));

        $webTestCaseMocked->expects($this->once())
            ->method('loadFixture')
            ->with(array('src/Cosma/Fixture/Table/User.yml', 'src/Cosma/Fixture/Table/Group.yml'))
            ->will($this->returnValue($objects));

        $methodAppendTableFixturesPath = new \ReflectionMethod(
            'Cosma\Bundle\TestingBundle\TestCase\WebTestCase',
            'appendTableFixturesPath'
        );
        $methodAppendTableFixturesPath->setAccessible(true);

        $this->assertEquals(12345, $methodAppendTableFixturesPath->invoke($webTestCaseMocked, array('User', 'Group')));

        $methodLoadFixture = new \ReflectionMethod(
            'Cosma\Bundle\TestingBundle\TestCase\WebTestCase',
            'loadFixture'
        );
        $methodLoadFixture->setAccessible(true);

        $methodLoadTableFixtures = new \ReflectionMethod(
            'Cosma\Bundle\TestingBundle\TestCase\WebTestCase',
            'loadTableFixtures'
        );
        $methodLoadTableFixtures->setAccessible(true);

        /** @var ArrayCollection $objects */
        $objects = $methodLoadTableFixtures->invoke($webTestCaseMocked, array('User', 'Group'), true);

        $this->assertCount(2, $objects, 'Has to return an collection of two entities');
        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\TestCase\ExampleEntity', $objects->first(), 'This object should be an entity ExampleEntity');
        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\TestCase\ExampleEntity', $objects->last(), 'This object should be an entity ExampleEntity');
    }

    /**
     * @return WebTestCase
     */
    private function getMockedWebTestCase()
    {
        $currentBundle = $this->getCurrentBundle();

        $entityRepository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableAutoload()
            ->getMock();

        $entityManager = $this->getEntityManager($entityRepository);

        $container = $this->getContainer($entityManager);

        $client = $this->getClient($container);

        return $this->getDefaultMockedWebTestCase($client, $currentBundle);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getCurrentBundle()
    {
        $currentBundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\BundleInterface')
            ->disableAutoload()
            ->setMethods(array('getName', 'getNameSpace'))
            ->getMock();
        $currentBundle->expects($this->any())
            ->method('getName')
            ->with()
            ->will($this->returnValue('BundleName'));
        $currentBundle->expects($this->any())
            ->method('getNameSpace')
            ->with()
            ->will($this->returnValue('Bundles\BundleName'));

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
            ->with('BundleName:ExampleEntity')
            ->will($this->returnValue($entityRepository));

        return $entityManager;
    }

    /**
     * @param EntityManager $entityManager
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getContainer(EntityManager $entityManager)
    {
        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableAutoload()
            ->setMethods(array('get'))
            ->getMock();
        $container->expects($this->any())
            ->method('get')
            ->with('doctrine.orm.entity_manager')
            ->will($this->returnValue($entityManager));

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
     * @param Client          $client
     * @param BundleInterface $currentBundle
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getDefaultMockedWebTestCase(Client $client, BundleInterface $currentBundle)
    {
        $webTestCaseMocked = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
            ->disableAutoload()
            ->setMethods(array('setUpBeforeClass'))
            ->getMockForAbstractClass();

        $reflectionClassMocked = new \ReflectionClass($webTestCaseMocked);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $clientProperty = $reflectionClass->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($webTestCaseMocked, $client);

        $currentBundleProperty = $reflectionClass->getProperty('currentBundle');
        $currentBundleProperty->setAccessible(true);
        $currentBundleProperty->setValue($webTestCaseMocked, $currentBundle);

        $fixturePathProperty = $reflectionClass->getProperty('fixturePath');
        $fixturePathProperty->setAccessible(true);
        $fixturePathProperty->setValue($webTestCaseMocked, 'src/Cosma/TestingBundle/TestCase/WebTestCase');

        $entityNameSpaceProperty = $reflectionClass->getProperty('fixturePath');
        $entityNameSpaceProperty->setAccessible(true);
        $entityNameSpaceProperty->setValue($webTestCaseMocked, 'Cosma\Bundle\TestingBundle\TestCase');

        return $webTestCaseMocked;
    }

    private function createCompiledContainerForConfig($config, $debug = false)
    {
        $container = $this->createContainer($debug);
        $container->registerExtension(new TestingExtension());
        $container->loadFromExtension('cosma_testing', $config);
        $this->compileContainer($container);

        return $container;
    }

    private function createContainer($debug = false)
    {
        $container = new ContainerBuilder(new ParameterBag(array(
            'kernel.cache_dir' => __DIR__,
            'kernel.charset'   => 'UTF-8',
            'kernel.debug'     => $debug,
        )));

        return $container;
    }

    private function compileContainer(ContainerBuilder $container)
    {
        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();
    }
}

class WebTestCaseExample extends WebTestCase
{
}

class ExampleEntity
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
    public function setFirestName($firstName)
    {
        $this->firstName = $firstName;
    }
}





