<?php
namespace Cosma\Bundle\TestingBundle\Tests\TestCase;

use Cosma\Bundle\TestingBundle\DependencyInjection\TestingExtension;
use Cosma\Bundle\TestingBundle\TestCase\WebTestCase;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class WebTestCaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see WebTestCase
     */
    public function testStaticAttributes()
    {
        $this->assertClassHasStaticAttribute('client', 'Cosma\Bundle\TestingBundle\TestCase\WebTestCase');
        $this->assertClassHasStaticAttribute('fixturePath', 'Cosma\Bundle\TestingBundle\TestCase\WebTestCase');
        $this->assertClassHasStaticAttribute('entityNameSpace', 'Cosma\Bundle\TestingBundle\TestCase\WebTestCase');
        $this->assertClassHasStaticAttribute('currentBundle', 'Cosma\Bundle\TestingBundle\TestCase\WebTestCase');
        $this->assertClassHasStaticAttribute('container', 'Cosma\Bundle\TestingBundle\TestCase\WebTestCase');
    }

    /**
     * @see WebTestCase::getMockedEntityWithId
     */
    public function testGetMockedEntityWithId()
    {
        /** @var WebTestCase $webTestCaseMocked */
        $webTestCaseMocked = $this->getMockForAbstractClass('Cosma\Bundle\TestingBundle\TestCase\WebTestCase');

        $method = new \ReflectionMethod(
            'Cosma\Bundle\TestingBundle\TestCase\WebTestCase',
            'getMockedEntityWithId'
        );
        $method->setAccessible(TRUE);

        /** @var ExampleEntity $mockedEntity */
        $mockedEntity = $method->invoke($webTestCaseMocked, 'Cosma\Bundle\TestingBundle\Tests\TestCase\ExampleEntity', 12345);

        $this->assertEquals(12345, $mockedEntity->getId());

    }

    /**
     * @see WebTestCase::getMockedEntityWithId
     * @expectedException \Doctrine\ORM\EntityNotFoundException
     */
    public function testGetMockedEntityWithId_Exception()
    {
        /** @var WebTestCase $webTestCaseMocked */
        $webTestCaseMocked = $this->getMockForAbstractClass('Cosma\Bundle\TestingBundle\TestCase\WebTestCase');

        $method = new \ReflectionMethod(
            'Cosma\Bundle\TestingBundle\TestCase\WebTestCase',
            'getMockedEntityWithId'
        );
        $method->setAccessible(TRUE);

        $method->invoke($webTestCaseMocked, 'XX\XXXXX', 12345);
    }

    /**
     * @see WebTestCase::getEntityWithId
     */
    public function testGetEntityWithId()
    {
        /** @var WebTestCase $webTestCaseMocked */
        $webTestCaseMocked = $this->getMockForAbstractClass('Cosma\Bundle\TestingBundle\TestCase\WebTestCase');

        $method = new \ReflectionMethod(
            'Cosma\Bundle\TestingBundle\TestCase\WebTestCase',
            'getEntityWithId'
        );
        $method->setAccessible(TRUE);


        /** @var ExampleEntity $entity */
        $entity = $method->invoke($webTestCaseMocked, 'Cosma\Bundle\TestingBundle\Tests\TestCase\ExampleEntity', 12345);

        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\TestCase\ExampleEntity', $entity);
        $this->assertEquals(12345, $entity->getId());
    }

    /**
     * @see WebTestCase::getEntityWithId
     * @expectedException \Doctrine\ORM\EntityNotFoundException
     */
    public function testGetEntityWithId_Exception()
    {

        /** @var WebTestCase $webTestCaseMocked */
        $webTestCaseMocked = $this->getMockForAbstractClass('Cosma\Bundle\TestingBundle\TestCase\WebTestCase');

        $method = new \ReflectionMethod(
            'Cosma\Bundle\TestingBundle\TestCase\WebTestCase',
            'getEntityWithId'
        );
        $method->setAccessible(TRUE);
        $method->returnsReference();

        $method->invoke($webTestCaseMocked, 'XX\XXXXX', 12345);
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
            ->setMethods(array('appendTableFixturesPath', 'loadFixture', 'setUpBeforeClass'))
            ->getMockForAbstractClass();

        $webTestCaseMocked->expects($this->once())
            ->method('appendTableFixturesPath')
            ->with(array('User', 'Group'))
            ->will($this->returnValue(array('src/Cosma/Fixture/Table/User.yml', 'src/Cosma/Fixture/Table/Group.yml')));

        $webTestCaseMocked->expects($this->once())
            ->method('appendTableFixturesPath')
            ->with(array('User', 'Group'))
            ->will($this->returnValue(array('src/Cosma/Fixture/Table/User.yml', 'src/Cosma/Fixture/Table/Group.yml')));



        $methodAppendTableFixturesPath = new \ReflectionMethod(
            'Cosma\Bundle\TestingBundle\TestCase\WebTestCase',
            'appendTableFixturesPath'
        );
        $methodAppendTableFixturesPath->setAccessible(TRUE);
        $this->assertEquals(12345,$methodAppendTableFixturesPath->invoke($webTestCaseMocked, array('User', 'Group')));
//
//
//
//        $methodLoadFixture = new \ReflectionMethod(
//            'Cosma\Bundle\TestingBundle\TestCase\WebTestCase',
//            'loadFixture'
//        );
//        $methodLoadFixture->setAccessible(TRUE);
//
//        $webTestCaseMocked->expects($this->once())
//            ->method('loadFixture')
//            ->with(array('src/Cosma/Fixture/Table/User.yml', 'src/Cosma/Fixture/Table/Group.yml'))
//            ->will($this->returnValue($objects));
//
//        $methodLoadTableFixtures = new \ReflectionMethod(
//            'Cosma\Bundle\TestingBundle\TestCase\WebTestCase',
//            'loadTableFixtures'
//        );
//        $methodLoadTableFixtures->setAccessible(TRUE);
//
//        $result = $methodLoadTableFixtures->invoke($webTestCaseMocked, array('User', 'Group'), true);
//
//        print_r($result);


    }





//    /**
//     * @see WebTestCase::getClient
//     */
//    public function testGetClient()
//    {
//        $client = $this->getClient();
//        $this->assertInstanceOf('Symfony\Bundle\FrameworkBundle\Client', $client, 'must return a client');
//    }
//
//    /**
//     * @see WebTestCase::getContainer
//     */
//    public function testGetContainer()
//    {
//        $container = $this->getContainer();
//
//        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerInterface', $container, 'must return a container');
//    }
//
//    /**
//     * @see WebTestCase::getEntityManager
//     */
//    public function testGetEntityManager()
//    {
//        $entityManager = $this->getEntityManager();
//
//        $this->assertInstanceOf('Doctrine\ORM\EntityManager', $entityManager, 'must return a EntityManager');
//    }
//
//    /**
//     * @see WebTestCase::getEntityRepository
//     */
//    public function testGetEntityRepository()
//    {
//        $entityRepository = $this->getEntityRepository('User');
//
//        $this->assertInstanceOf('Doctrine\ORM\EntityRepository', $entityRepository, 'must return a EntityRepository');
//    }
//
//    /**
//     * @see WebTestCase::getMockedEntityWithId
//     */
//    public function testGetMockedEntityWithId_FullNamespace()
//    {
//        /** @var User $mockedUser */
//        $mockedUser = $this->getMockedEntityWithId('Cosma\Bundle\TestingBundle\Entity\User', 12345);
//        $this->assertEquals(12345, $mockedUser->getId());
//    }
//
//    /**
//     * @see WebTestCase::getMockedEntityWithId
//     * @expectedException \Doctrine\ORM\EntityNotFoundException
//     */
//    public function testGetMockedEntityWithId_Exception()
//    {
//        $this->getMockedEntityWithId('xcc\xxxxxxx', 12345);
//    }
//
//    /**
//     * @see WebTestCase::getMockedEntityWithId
//     */
//    public function testGetMockedEntityWithId_EntityName()
//    {
//        /** @var User $mockedUser */
//        $mockedUser = $this->getMockedEntityWithId('User', 12345);
//        $this->assertEquals(12345, $mockedUser->getId());
//    }
//
//    /**
//     * @see WebTestCase::getEntityWithId
//     */
//    public function testGetEntityWithId_FullNamespace()
//    {
//
//        /** @var User $user */
//        $user = $this->getEntityWithId('Cosma\Bundle\TestingBundle\Entity\User', 12345);
//        $this->assertEquals(12345, $user->getId());
//    }
//
//    /**
//     * @see WebTestCase::getEntityWithId
//     */
//    public function testGetEntityWithId()
//    {
//        /** @var User $user */
//        $user = $this->getEntityWithId('User', 12345);
//        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Entity\User', $user);
//        $this->assertEquals(12345, $user->getId());
//    }
//
//    /**
//     * @see WebTestCase::getEntityWithId
//     * @expectedException \Doctrine\ORM\EntityNotFoundException
//     */
//    public function testGetEntityWithId_Exception()
//    {
//        $this->getEntityWithId('xxxx\xxxx', 12345);
//    }
//
//    /**
//     * @see WebTestCase::loadTableFixtures
//     */
//    public function testLoadTableFixtures_ManyToOne()
//    {
//        $this->loadTableFixtures(
//            array('Group', 'User', 'Address')
//        );
//
//        $userRepository = $this->getEntityRepository('User');
//
//        /** @var User $user */
//        $user = $userRepository->findOneByName('Adah Reichel');
//
//        $this->assertEquals('Adams-Reichel', $user->getGroup()->getName(), 'Many to one relation is wrong');
//    }
//
//    /**
//     * @see WebTestCase::loadTableFixtures
//     */
//    public function testLoadTableFixtures_ManyToMany()
//    {
//        $this->loadTableFixtures(
//            array('Group', 'User', 'Address')
//        );
//
//        $userRepository = $this->getEntityRepository('User');
//
//        $addressRepository = $this->getEntityRepository('Address');
//
//        /** @var User $user */
//        $user = $userRepository->findOneByName('Adah Reichel');
//
//        $addresses = $user->getAddresses();
//
//        $this->assertEquals(2, $addresses->count(), 'Many to Many relation is wrong');
//
//        /** @var Address $addressOne */
//        $addressOne = $addressRepository->findOneByStreetName('Adams Roads');
//
//        /** @var Address $addressOne */
//        $addressTwo = $addressRepository->findOneByStreetName('Steuber Skyway');
//
//        $this->assertContains($addressOne,  $addresses, 'Address One should be pat of collection');
//        $this->assertContains($addressTwo,  $addresses, 'Address Two should be part of collection');
//    }
//
//    /**
//     * @see WebTestCase::loadCustomFixtures
//     */
//    public function testLoadCustomFixtures_ManyToMany()
//    {
//        $this->loadCustomFixtures(
//            array('example')
//        );
//
//        $userRepository = $this->getEntityRepository('User');
//
//        $addressRepository = $this->getEntityRepository('Address');
//
//        /** @var User $user */
//        $user = $userRepository->findOneByName('Wanda Koelpin');
//
//        $addresses = $user->getAddresses();
//
//        $this->assertEquals(2, $addresses->count(), 'Many to Many relation is wrong');
//
//        /** @var Address $addressOne */
//        $addressOne = $addressRepository->findOneByStreetName('Adams Roads');
//
//        /** @var Address $addressOne */
//        $addressTwo = $addressRepository->findOneByStreetName('Steuber Skyway');
//
//        $this->assertContains($addressOne,  $addresses, 'Address One should be pat of collection');
//        $this->assertContains($addressTwo,  $addresses, 'Address Two should be part of collection');
//    }

    /**
     * @see WebTestCase::getClient
     */
    public function tttestSetUpBeforeClass()
    {
        $config = array(
        );

        $container = $this->createCompiledContainerForConfig($config);


        //$client  = new Client(new AppKernel('WebTestCaseTest', 'asda', 'dev', true));
        //$this->getMockForAbstractClass('Cosma\Bundle\TestingBundle\TestCase\WebTestCase');


//        $class = $this->getMockForAbstractClass(
//            'Cosma\Bundle\TestingBundle\TestCase\WebTestCase',          /* name of class to mock     */
//            array('createClient', 'getFixtureManager', 'getFixturePath', 'getEntityNameSpace') /* list of methods to mock   */
//        );
//        $class::staticExpects($this->any())
//            ->method('createClient')
//            ->will($this->returnValue(123));



        $webTestCaseMocked = $this->getMockForAbstractClass('Cosma\Bundle\TestingBundle\TestCase\WebTestCase',
            array('getFixtureManager', 'getFixturePath', 'getEntityNameSpace'));

        $webTestCaseMocked::staticExpects($this->any())
            ->method('createClient')
            ->will($this->returnValue(123));;





//        $webTestCaseMocked = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
//            ->disableOriginalConstructor()
//        ->setMethods(array('createClient', 'getFixtureManager', 'getFixturePath', 'getEntityNameSpace'))
//            ->getMockForAbstractClass();
//
//        $webTestCaseMocked::staticExpects($this->any())
//            ->method('createClient')
//            ->will($this->returnValue(123));;
//
//
//
//        $this->assertNull($webTestCaseMocked->setUpBeforeClass());



//        $webTestCaseMocked = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
//            ->disableOriginalConstructor()
//        ->setMethods(array('createClient', 'getFixtureManager', 'getFixturePath', 'getEntityNameSpace'))
//            ->getMockForAbstractClass();
//        $webTestCaseMocked->expects($this->once())
//            ->method('setUpBeforeClass')
//            ->with()
//            ->will($this->returnValue(null));

//        $this->assertNull($webTestCaseMocked->setUpBeforeClass());










        //$testCaseExample = new WebTestCaseExample();
        //print_r($testCaseExample);

        //$testCaseExample->setUpBeforeClass();




        //$this->assertInstanceOf('Solarium\Client', $container->get('cosma_testing'));
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





