<?php
namespace Cosma\Bundle\TestingBundle\Tests\TestCase;

use Cosma\Bundle\TestingBundle\TestCase\SimpleTestCase;

class SimpleTestCaseTest extends SimpleTestCase
{
    /**
     * @see SimpleTestCase::getMockedEntityWithId
     */
    public function testGetMockedEntityWithId()
    {
        /** @var ExampleEntity $mockedEntity */
        $mockedEntity = $this->getMockedEntityWithId('Cosma\Bundle\TestingBundle\Tests\TestCase\ExampleEntity', 12345);
        $this->assertEquals(12345, $mockedEntity->getId());
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
        /** @var ExampleEntity $entity */
        $entity = $this->getEntityWithId('Cosma\Bundle\TestingBundle\Tests\TestCase\ExampleEntity', 12345);
        $this->assertInstanceOf('Cosma\Bundle\TestingBundle\Tests\TestCase\ExampleEntity', $entity);
        $this->assertEquals(12345, $entity->getId());
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