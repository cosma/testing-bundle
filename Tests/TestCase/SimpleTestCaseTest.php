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

use Cosma\Bundle\TestingBundle\TestCase\SimpleTestCase;

/**
 * @retry 6
 */
class SimpleTestCaseTest extends SimpleTestCase
{
    /**
     * @type int
     */
    private static $counterFirstTest = 0;
    /**
     * @type int
     */
    private static $counterSecondTest = 0;
    /**
     * @type int
     */
    private static $counterThirdTest = 0;
    /**
     * @type int
     */
    private static $counterForthTest = 0;


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

    /**
     * @see                      SimpleTestCase::runBare
     *
     * @expectedException \Exception
     *
     * @expectedExceptionMessage This test needs at least 6 retries
     */
    public function testRunBare_NoRetry()
    {
        self::$counterFirstTest++;

        if (self::$counterFirstTest > 6) {
            $this->assertTrue(true);
        } else {
            throw new \Exception('This test needs at least 6 retries');
        }
    }

    /**
     * @see                      SimpleTestCase::runBare
     *
     * @retry                    4
     *
     * @expectedException \Exception
     *
     * @expectedExceptionMessage This test needs at least 6 retries
     */
    public function testRunBare_NotEnough()
    {
        self::$counterSecondTest++;

        if (self::$counterSecondTest > 6) {
            $this->assertTrue(true);
        } else {
            throw new \Exception('This test needs at least 6 retries');
        }
    }

    /**
     * @see                      SimpleTestCase::runBare
     *
     * @retry                    10
     *
     */
    public function testRunBare_MethodRetry()
    {
        self::$counterThirdTest++;

        if (self::$counterThirdTest > 6) {
            $this->assertTrue(true);
        } else {
            throw new \Exception('This test needs at least 6 retries');
        }
    }

    /**
     * @see                      SimpleTestCase::runBare
     *
     */
    public function testRunBare_ClassRetry()
    {
        self::$counterForthTest++;

        if (self::$counterForthTest > 6) {
            $this->assertTrue(true);
        } else {
            throw new \Exception('This test needs at least 6 retries');
        }
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