<?php
namespace Cosma\Bundle\TestingBundle\Tests\TestCase;

use Cosma\Bundle\TestingBundle\Entity\Address;
use Cosma\Bundle\TestingBundle\Entity\User;
use Cosma\Bundle\TestingBundle\TestCase\WebTestCase;

class WebTestCaseTest extends WebTestCase
{

    /**
     * @return void
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

    }

    /**
     * @see WebTestCase::getClient
     */
    public function testGetClient()
    {
        die();
        $client = $this->getClient();




        $this->assertInstanceOf('Symfony\Bundle\FrameworkBundle\Client', $client, 'must return a client');
    }

    /**
     * @see WebTestCase::getContainer
     */
    public function testGetContainer()
    {
        $container = $this->getContainer();

        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerInterface', $container, 'must return a container');
    }

    /**
     * @see WebTestCase::getEntityManager
     */
    public function testGetEntityManager()
    {
        $entityManager = $this->getEntityManager();

        $this->assertInstanceOf('Doctrine\ORM\EntityManager', $entityManager, 'must return a EntityManager');
    }

    /**
     * @see WebTestCase::getEntityRepository
     */
    public function testGetEntityRepository()
    {
        $entityRepository = $this->getEntityRepository('User');

        $this->assertInstanceOf('Doctrine\ORM\EntityRepository', $entityRepository, 'must return a EntityRepository');
    }

    /**
     * @see WebTestCase::getMockedEntityWithId
     */
    public function testGetMockedEntityWithId_FullNamespace()
    {
        /** @var User $mockedUser */
        $mockedUser = $this->getMockedEntityWithId('Cosma\Bundle\TestingBundle\Entity\User', 12345);
        $this->assertEquals(12345, $mockedUser->getId());
    }

    /**
     * @see WebTestCase::getMockedEntityWithId
     * @expectedException \Doctrine\ORM\EntityNotFoundException
     */
    public function testGetMockedEntityWithId_Exception()
    {
        $this->getMockedEntityWithId('xcc\xxxxxxx', 12345);
    }

    /**
     * @see WebTestCase::getMockedEntityWithId
     */
    public function testGetMockedEntityWithId_EntityName()
    {
        /** @var User $mockedUser */
        $mockedUser = $this->getMockedEntityWithId('User', 12345);
        $this->assertEquals(12345, $mockedUser->getId());
    }

    /**
     * @see WebTestCase::getEntityWithId
     */
    public function testGetEntityWithId_FullNamespace()
    {

        /** @var User $user */
        $user = $this->getEntityWithId('Cosma\Bundle\TestingBundle\Entity\User', 12345);
        $this->assertEquals(12345, $user->getId());
    }

    /**
     * @see WebTestCase::getEntityWithId
     */
    public function testGetEntityWithId()
    {
        /** @var User $user */
        $user = $this->getEntityWithId('User', 12345);
        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Entity\User', $user);
        $this->assertEquals(12345, $user->getId());
    }

    /**
     * @see WebTestCase::getEntityWithId
     * @expectedException \Doctrine\ORM\EntityNotFoundException
     */
    public function testGetEntityWithId_Exception()
    {
        $this->getEntityWithId('xxxx\xxxx', 12345);
    }

    /**
     * @see WebTestCase::loadTableFixtures
     */
    public function testLoadTableFixtures_ManyToOne()
    {
        $this->loadTableFixtures(
            array('Group', 'User', 'Address')
        );

        $userRepository = $this->getEntityRepository('User');

        /** @var User $user */
        $user = $userRepository->findOneByName('Adah Reichel');

        $this->assertEquals('Adams-Reichel', $user->getGroup()->getName(), 'Many to one relation is wrong');
    }

    /**
     * @see WebTestCase::loadTableFixtures
     */
    public function testLoadTableFixtures_ManyToMany()
    {
        $this->loadTableFixtures(
            array('Group', 'User', 'Address')
        );

        $userRepository = $this->getEntityRepository('User');

        $addressRepository = $this->getEntityRepository('Address');

        /** @var User $user */
        $user = $userRepository->findOneByName('Adah Reichel');

        $addresses = $user->getAddresses();

        $this->assertEquals(2, $addresses->count(), 'Many to Many relation is wrong');

        /** @var Address $addressOne */
        $addressOne = $addressRepository->findOneByStreetName('Adams Roads');

        /** @var Address $addressOne */
        $addressTwo = $addressRepository->findOneByStreetName('Steuber Skyway');

        $this->assertContains($addressOne,  $addresses, 'Address One should be pat of collection');
        $this->assertContains($addressTwo,  $addresses, 'Address Two should be part of collection');
    }

    /**
     * @see WebTestCase::loadCustomFixtures
     */
    public function testLoadCustomFixtures_ManyToMany()
    {
        $this->loadCustomFixtures(
            array('example')
        );

        $userRepository = $this->getEntityRepository('User');

        $addressRepository = $this->getEntityRepository('Address');

        /** @var User $user */
        $user = $userRepository->findOneByName('Wanda Koelpin');

        $addresses = $user->getAddresses();

        $this->assertEquals(2, $addresses->count(), 'Many to Many relation is wrong');

        /** @var Address $addressOne */
        $addressOne = $addressRepository->findOneByStreetName('Adams Roads');

        /** @var Address $addressOne */
        $addressTwo = $addressRepository->findOneByStreetName('Steuber Skyway');

        $this->assertContains($addressOne,  $addresses, 'Address One should be pat of collection');
        $this->assertContains($addressTwo,  $addresses, 'Address Two should be part of collection');
    }
} 