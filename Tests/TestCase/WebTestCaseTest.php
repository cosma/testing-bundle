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

use h4cc\AliceFixturesBundle\Fixtures\FixtureManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Client;

use Cosma\Bundle\TestingBundle\TestCase\WebTestCase;
use h4cc\AliceFixturesBundle\Fixtures\FixtureManager as AliceFixtureManager;
use Symfony\Component\HttpKernel\HttpKernel;

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
     * @see Cosma\Bundle\TestingBundle\TestCase\WebTestCase::setUpBeforeClass
     */
    public function testSetUpBeforeClass()
    {

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
            ->disableAutoload()
            ->disableOriginalConstructor()
            ->setMethods(array('shutdown', 'boot', 'getBundles'))
            ->getMock();


        /** @var WebTestCase $webTestCase */
        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\WebTestCase')
            ->disableAutoload()
            ->disableOriginalConstructor()
            ->setMethods(array('bootKernel', 'createKernel'))
            ->getMockForAbstractClass();

        $reflectionClass       = new \ReflectionClass($webTestCase);

        $clientProperty = $reflectionClass->getProperty('kernel');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($webTestCase, $kernel);

        $reflectionMethod = $reflectionClass->getMethod('setUpBeforeClass');

        $reflectionMethod->invoke($webTestCase);


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


