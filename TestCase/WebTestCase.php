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


namespace Cosma\Bundle\TestingBundle\TestCase;

use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as WebTestCaseBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use h4cc\AliceFixturesBundle\Fixtures\FixtureManager;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

abstract class WebTestCase extends WebTestCaseBase
{
    /**
     * @var Client
     */
    private static $client;

    /**
     * @var string
     */
    private static $fixturePath;

    /**
     * @var string
     */
    private static $entityNameSpace;

    /**
     * @var BundleInterface
     */
    private static $currentBundle;

    /**
     * @return void
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$client = self::createClient();
        self::$client->followRedirects();

        /** @var FixtureManager $fixtureManager */
        $fixtureManager = static::getFixtureManager();

        $fixtureManager->getSchemaTool()->dropSchema();
        $fixtureManager->getSchemaTool()->createSchema();

        static::getFixturePath();
        static::getEntityNameSpace();
    }

    /**
     * @return Client
     */
    protected function getClient()
    {
        return self::$client;
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return self::$client->getContainer();
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @param $entity
     *
     * @return EntityRepository
     */
    protected function getEntityRepository($entity)
    {
        $repositoryName = self::getCurrentBundle()->getName() . ':' . $entity;

        return $this->getEntityManager()->getRepository($repositoryName);
    }

    /**
     * @param $entityClassName
     * @param $id
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     * @throws EntityNotFoundException
     */
    protected function getMockedEntityWithId($entityClassName, $id)
    {
        if (false === strpos($entityClassName, '\\')) {
            $entityClassName = static::getEntityNameSpace() . '\\' . $entityClassName;
        }

        if (!class_exists($entityClassName)) {
            throw new EntityNotFoundException();
        }

        $entityModel = $this->getMock($entityClassName, array('getId'));
        $entityModel
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($id));

        return $entityModel;
    }

    /**
     * @param $entityClassName
     * @param $id
     *
     * @return mixed
     * @throws EntityNotFoundException
     */
    protected function getEntityWithId($entityClassName, $id)
    {
        if (false === strpos($entityClassName, '\\')) {
            $entityClassName = $this->getEntityNameSpace() . '\\' . $entityClassName;
        }

        if (!class_exists($entityClassName)) {
            throw new EntityNotFoundException();
        }

        $entityObject = new $entityClassName;

        $reflectionObject   = new \ReflectionObject($entityObject);
        $reflectionProperty = $reflectionObject->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($entityObject, $id);

        return $entityObject;
    }

    /**
     * @param array $fixtures
     * @param bool  $dropDatabaseBefore
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function loadTableFixtures(array $fixtures, $dropDatabaseBefore = true)
    {
        if (0 == count($fixtures)) {
            throw new \InvalidArgumentException('Array is empty.');
        }

        $fixtures = $this->appendTableFixturesPath($fixtures);

        return $this->loadFixture($fixtures, $dropDatabaseBefore);
    }

    /**
     * @param array $fixtures
     * @param bool  $dropDatabaseBefore
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function loadTestFixtures(array $fixtures, $dropDatabaseBefore = true)
    {
        if (0 == count($fixtures)) {
            throw new \InvalidArgumentException('Array is empty.');
        }

        $debugTrace    = debug_backtrace();
        $testClassPath = $this->getTestClassPath($debugTrace);
        $fixtures      = $this->appendTestFixturesPath($fixtures, $testClassPath);

        return $this->loadFixture($fixtures, $dropDatabaseBefore);
    }

    /**
     * @param array $fixtures
     * @param bool  $dropDatabaseBefore
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function loadCustomFixtures(array $fixtures, $dropDatabaseBefore = true)
    {
        if (0 == count($fixtures)) {
            throw new \InvalidArgumentException('Array is empty.');
        }

        return $this->loadFixture($fixtures, $dropDatabaseBefore);
    }

    /**
     * @return string
     */
    private static function getEntityNameSpace()
    {
        if (null === self::$entityNameSpace) {
            $currentBundle = self::getCurrentBundle();

            self::$entityNameSpace = $currentBundle->getNamespace();

            if (self::$client->getContainer()->hasParameter("entity_namespace")) {
                self::$entityNameSpace .= '\\' . self::$client->getContainer()->getParameter("entity_namespace");
            } else {
                self::$entityNameSpace .= '\Entity';
            }
        }

        return self::$entityNameSpace;
    }

    /**
     * @return string
     */
    private static function getFixturePath()
    {
        if (null === self::$fixturePath) {
            $currentBundle = self::getCurrentBundle();

            self::$fixturePath = $currentBundle->getPath();

            if (self::$client->getContainer()->hasParameter('fixture_path')) {
                self::$fixturePath .= '/' . self::$client->getContainer()->getParameter('fixture_path');
            } else {
                self::$fixturePath .= '/Fixture';
            }
        }

        return self::$fixturePath;
    }

    /**
     * @return BundleInterface
     */
    private static function getCurrentBundle()
    {
        if (null === self::$currentBundle) {
            $bundles          = self::$client->getKernel()->getBundles();
            $currentTestClass = get_called_class();

            foreach ($bundles as $bundle) {
                if (false !== strpos($currentTestClass, $bundle->getNamespace())) {
                    self::$currentBundle = $bundle;
                }
            }
        }

        return self::$currentBundle;
    }

    /**
     * @param array $tablesFixtures
     *
     * @return array
     */
    private function appendTableFixturesPath(array $fixtures)
    {
        $fixturePaths = array();
        foreach ($fixtures as $tableFixture) {
            $fixturePaths[] = self::getFixturePath() . '/Table/' . $tableFixture . '.yml';
        }

        return $fixturePaths;
    }

    /**
     * @param array $fixtures
     * @param       $testClassPath
     *
     * @return array
     */
    private function appendTestFixturesPath(array $fixtures, $testClassPath)
    {
        $fixturePaths = array();
        foreach ($fixtures as $customFixture) {
            $fixturePaths[] = self::getFixturePath() . "/Test/{$testClassPath}/{$customFixture}.yml";
        }

        return $fixturePaths;
    }

    /**
     * @return FixtureManager
     */
    private static function getFixtureManager()
    {
        return self::$client->getContainer()->get('h4cc_alice_fixtures.manager');
    }

    /**
     * @param array $fixtures
     * @param       $dropDatabaseBefore
     *
     * @return array
     */
    private function loadFixture(array $fixtures, $dropDatabaseBefore)
    {
        $fixtureManager = static::getFixtureManager();
        if ($dropDatabaseBefore) {
            $fixtureManager->persist(array(), true);
        }

        $objects = $fixtureManager->loadFiles($fixtures);
        $fixtureManager->persist($objects);

        return $objects;
    }

    /**
     * @param array $debugTrace
     *
     * @return mixed
     */
    protected function getTestClassPath(array $debugTrace)
    {
        if (isset($debugTrace[0]['file'])) {
            $testPath      = strpos($debugTrace[0]['file'], "Tests/", 1);
            $filePath      = substr($debugTrace[0]['file'], $testPath + 6);
            $testClassPath = str_replace('.php', '', $filePath);
        } else {
            $testClassPath = '';
        }

        return $testClassPath;
    }
}
