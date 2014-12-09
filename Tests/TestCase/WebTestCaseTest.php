<?php
namespace Cosma\Bundle\TestingBundle\Tests\TestCase;

use Cosma\Bundle\TestingBundle\DependencyInjection\TestingExtension;
use Cosma\Bundle\TestingBundle\TestCase\WebTestCase;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Bundle\FrameworkBundle\Client;

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
     * @see WebTestCase::getClient
     */
    public function testGetClient()
    {

        $client = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Client')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        /** @var WebTestCase $webTestCaseMocked */
        $webTestCaseMocked = $this->getMockForAbstractClass('Cosma\Bundle\TestingBundle\TestCase\WebTestCase');

        $reflectionClass    = new \ReflectionClass($webTestCaseMocked);
        $reflectionProperty = $reflectionClass->getProperty('client');
        $reflectionProperty->setAccessible(true);

        $reflectionProperty->setValue($webTestCaseMocked, $client);

        $method = new \ReflectionMethod(
            'Cosma\Bundle\TestingBundle\TestCase\WebTestCase',
            'getClient'
        );
        $method->setAccessible(true);

        /** @var Client $mockedEntity */
        $result = $method->invoke($webTestCaseMocked);

        $this->assertEquals($client, $result, 'getCLient doesnt return a Client');
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
        $method->setAccessible(true);

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
        $method->setAccessible(true);

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
        $method->setAccessible(true);

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
        $method->setAccessible(true);
        $method->returnsReference();

        $method->invoke($webTestCaseMocked, 'XX\XXXXX', 12345);
    }

//    /**
//     * @see WebTestCase::getEntityWithId
//     * @expectedException \Doctrine\ORM\EntityNotFoundException
//     */
//    public function testLoadTableFixtures()
//    {
//        $entityOne = new ExampleEntity();
//        $entityOne->setName('One');
//
//        $entityTwo = new ExampleEntity();
//        $entityTwo->setName('Two');
//
//        $objects = new ArrayCollection();
//
//        $objects->add($entityOne);
//        $objects->add($entityTwo);
//
//
//        $webTestCaseMocked = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
//            ->disableOriginalConstructor()
//            ->setMethods(array('appendTableFixturesPath', 'loadFixture'))
//            ->getMockForAbstractClass();
//
//        $webTestCaseMocked->expects($this->once())
//            ->method('appendTableFixturesPath')
//            ->with(array('User', 'Group'))
//            ->will($this->returnValue(array('src/Cosma/Fixture/Table/User.yml', 'src/Cosma/Fixture/Table/Group.yml')));
//
//        $webTestCaseMocked->expects($this->once())
//            ->method('loadFixture')
//            ->with(array('src/Cosma/Fixture/Table/User.yml', 'src/Cosma/Fixture/Table/Group.yml'))
//            ->will($this->returnValue($objects));
//
//
//        $methodAppendTableFixturesPath = new \ReflectionMethod(
//            'Cosma\Bundle\TestingBundle\TestCase\WebTestCase',
//            'appendTableFixturesPath'
//        );
//        $methodAppendTableFixturesPath->setAccessible(TRUE);
//
//        $this->assertEquals(12345,$methodAppendTableFixturesPath->invoke($webTestCaseMocked, array('User', 'Group')));
//
//        $methodLoadFixture = new \ReflectionMethod(
//            'Cosma\Bundle\TestingBundle\TestCase\WebTestCase',
//            'loadFixture'
//        );
//        $methodLoadFixture->setAccessible(TRUE);
//
//
//        $methodLoadTableFixtures = new \ReflectionMethod(
//            'Cosma\Bundle\TestingBundle\TestCase\WebTestCase',
//            'loadTableFixtures'
//        );
//        $methodLoadTableFixtures->setAccessible(TRUE);
//
//        /** @var ArrayCollection $objects */
//        $objects = $methodLoadTableFixtures->invoke($webTestCaseMocked, array('User', 'Group'), true);
//
//        $this->assertCount(2, $objects, 'Has to return an collection of two entities');
//        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\TestCase\ExampleEntity', $objects->first(), 'This object should be an entity ExampleEntity');
//        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\TestCase\ExampleEntity', $objects->last(), 'This object should be an entity ExampleEntity');
//
//
//    }

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





