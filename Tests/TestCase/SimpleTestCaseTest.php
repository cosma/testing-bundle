<?php
namespace Cosma\Bundle\TestingBundle\Tests\TestCase;

use Cosma\Bundle\TestingBundle\Entity\User;
use Cosma\Bundle\TestingBundle\TestCase\SimpleTestCase;

class SimpleTestCaseTest extends SimpleTestCase
{
    /**
     * @see SimpleTestCase::getMockedEntityWithId
     */
    public function testGetMockedEntityWithId()
    {
        /** @var User $mockedUser */
        $mockedUser = $this->getMockedEntityWithId('Cosma\Bundle\TestingBundle\Tests\TestCase\ExampleEntity', 12345);
        $this->assertEquals(12345, $mockedUser->getId());
    }

    /**
     * @see SimpleTestCase::getMockedEntityWithId
     * @expectedException \Doctrine\ORM\EntityNotFoundException
     */
    public function testGetMockedEntityWithId_Exception()
    {
        $this->getMockedEntityWithId('xxxxxxxx', 12345);
    }

    /**
     * @see SimpleTestCase::getEntityWithId
     */
    public function testGetEntityWithId()
    {
        /** @var User $user */
        $user = $this->getEntityWithId('Cosma\Bundle\TestingBundle\Tests\TestCase\ExampleEntity', 12345);
        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\TestCase\ExampleEntity', $user);
        $this->assertEquals(12345, $user->getId());
    }

    /**
     * @see SimpleTestCase::getEntityWithId
     * @expectedException \Doctrine\ORM\EntityNotFoundException
     */
    public function testGetEntityWithId_Exception()
    {
        $this->getEntityWithId('xxxxxxxx', 12345);
    }
}


class ExampleEntity {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
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