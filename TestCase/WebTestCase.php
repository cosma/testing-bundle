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

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as WebTestCaseBase;
use Doctrine\ORM\EntityManager;
use h4cc\AliceFixturesBundle\Fixtures\FixtureManager;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

abstract class WebTestCase extends WebTestCaseBase
{
    /**
     * @var BundleInterface
     */
    private static $currentBundle;

    /**
     * @var FixtureManager
     */
    private static $fixtureManager;

    /**
     * @var string
     */
    private static $fixturePath;

    /**
     * @return void
     */
    public static function setUpBeforeClass()
    {
        static::bootKernel();

        static::getCurrentBundle();
        static::getFixtureManager();
        static::getFixturePath();
    }

    /**
     * Clean up Kernel usage in this test.
     */
    public static function tearDownAfterClass()
    {
        self::$currentBundle = NULL;
        self::$fixtureManager = NULL;
        self::$fixturePath = NULL;

        static::ensureKernelShutdown();
    }

    /**
     * @return void
     */
    protected function setUp()
    {
        static::bootKernel();
    }

    /**
     * @param array $server
     *
     * @return Client
     */
    protected function getClient(array $server = array())
    {
        /** @var Client $client */
        $client = static::$kernel->getContainer()->get('test.client');

        $client->setServerParameters($server);

        return $client;
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return static::$kernel->getContainer()->get('doctrine')->getManager();
    }

    /**
     * @param $entityName
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getEntityRepository($entityName)
    {
        if (FALSE !== strpos($entityName, ':')) {
            $entityDescription = explode(':', $entityName);

            $bundleName = $entityDescription[0];
            $entityName = $entityDescription[1];

        }else{
            $bundleName = static::getCurrentBundle()->getName();
        }

        return $this->getEntityManager()->getRepository($bundleName . ':' . $entityName);
    }

    /**
     * @param $entity
     * @param $id
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     * @throws EntityNotFoundException
     */
    protected function getMockedEntityWithId($entity, $id)
    {
        $entityClass = $this->getFullPathEntity($entity);

        if (!class_exists($entityClass)) {
            throw new EntityNotFoundException();
        }

        $entityModel = $this->getMock($entityClass, array('getId'));
        $entityModel
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($id));

        return $entityModel;
    }

    /**
     * @param $entity
     * @param $id
     *
     * @return mixed
     * @throws EntityNotFoundException
     */
    protected function getEntityWithId($entity, $id)
    {
        $entityClass = $this->getFullPathEntity($entity);

        if (!class_exists($entityClass)) {
            throw new EntityNotFoundException();
        }

        $entityObject = new $entityClass;

        $reflectionObject = new \ReflectionObject($entityObject);
        $reflectionProperty = $reflectionObject->getProperty('id');
        $reflectionProperty->setAccessible(TRUE);
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
    protected function loadTableFixtures(array $fixtures, $dropDatabaseBefore = TRUE)
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
    protected function loadTestFixtures(array $fixtures, $dropDatabaseBefore = TRUE)
    {
        if (0 == count($fixtures)) {
            throw new \InvalidArgumentException('Array is empty.');
        }

        $debugTrace = debug_backtrace();
        $testClassPath = $this->getTestClassPath($debugTrace);
        $fixtures = $this->appendTestFixturesPath($fixtures, $testClassPath);

        return $this->loadFixture($fixtures, $dropDatabaseBefore);
    }

    /**
     * @param array $fixtures
     * @param bool  $dropDatabaseBefore
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function loadCustomFixtures(array $fixtures, $dropDatabaseBefore = TRUE)
    {
        if (0 == count($fixtures)) {
            throw new \InvalidArgumentException('Array is empty.');
        }

        $fixtures = $this->appendFixturesPath($fixtures);

        return $this->loadFixture($fixtures, $dropDatabaseBefore);
    }

    /**
     * @param array $debugTrace
     *
     * @return mixed
     */
    protected function getTestClassPath(array $debugTrace)
    {
        if (isset($debugTrace[0]['file'])) {
            $testPath = strpos($debugTrace[0]['file'], "Tests/", 1);
            $filePath = substr($debugTrace[0]['file'], $testPath + 6);
            $testClassPath = str_replace('.php', '', $filePath);
        } else {
            $testClassPath = '';
        }

        return $testClassPath;
    }

    /**
     * @return BundleInterface
     */
    private static function getCurrentBundle()
    {
        if (NULL === self::$currentBundle) {
            $bundles = static::$kernel->getBundles();
            $currentTestClass = get_called_class();

            foreach ($bundles as $bundle) {
                if (0 === strpos($currentTestClass, $bundle->getNamespace())) {
                    self::$currentBundle = $bundle;
                }
            }
        }

        return self::$currentBundle;
    }

    /**
     * @param $bundleName
     *
     * @return \Symfony\Component\HttpKernel\Bundle\BundleInterface
     * @throws \Exception
     */
    private function getBundleByName($bundleName)
    {
        $bundles = static::$kernel->getBundles();
        foreach ($bundles as $bundle) {
            if ($bundleName == $bundle->getName()) {
                return $bundle;
            }
        }
        throw new \Exception("Bundle not found: {$bundle}");
    }

    /**
     * @return FixtureManager
     */
    private static function getFixtureManager()
    {
        if (NULL === self::$fixtureManager) {
            self::$fixtureManager = static::$kernel->getContainer()->get('h4cc_alice_fixtures.manager');
        }

        return self::$fixtureManager;
    }

    /**
     * @return string
     */
    private static function getFixturePath()
    {
        if (NULL === self::$fixturePath) {
            $fixturePath = static::getCurrentBundle()->getPath() . '/' .
                static::$kernel->getContainer()->getParameter('cosma_testing.fixture_path');

            self::$fixturePath = $fixturePath;
        }

        return self::$fixturePath;
    }

    /**
     * @param $entity
     *
     * @return mixed
     */
    private function getFullPathEntity($entity)
    {

        if (FALSE !== strpos($entity, '\\')) {
            return $entity;
        }

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();

        if (FALSE !== strpos($entity, ':')) {
            $entityDescription = explode(':', $entity);

            $bundleName = $entityDescription[0];
            $entityName = $entityDescription[1];

            $bundle = $this->getBundleByName($bundleName);

            $fullPathEntity = $this->getEntityNamespaceForBundle($entityManager, $bundle) .
                '\\' .
                $entityName;

            return $fullPathEntity;
        }

        $fullPathEntity = $this->getEntityNamespaceForBundle($entityManager, static::getCurrentBundle()) .
            '\\' .
            $entity;

        return $fullPathEntity;
    }

    /**
     * @param \Doctrine\ORM\EntityManager                          $entityManager
     * @param \Symfony\Component\HttpKernel\Bundle\BundleInterface $bundle
     *
     * @return mixed
     */
    private function getEntityNamespaceForBundle(EntityManager $entityManager, BundleInterface $bundle)
    {
        $metadataCollection = $entityManager->getMetadataFactory()->getAllMetadata();
        /** @var ClassMetadata $m */
        foreach ($metadataCollection as $metadata) {
            if (0 === strpos($metadata->namespace, $bundle->getNamespace())) {
                return $metadata->namespace;
            }
        }
    }

    /**
     * @param array $fixtures
     *
     * @return array
     */
    private function appendTableFixturesPath(array $fixtures)
    {
        $fixturePath = static::getFixturePath() . '/';
        $fixturePath .= static::$kernel->getContainer()->getParameter('cosma_testing.fixture_table_directory');

        $fixturePaths = array();
        foreach ($fixtures as $fixture) {
            $fixturePaths[] = "{$fixturePath}/{$fixture}.yml";
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
        $fixturePath = static::getFixturePath() . '/';
        $fixturePath .= static::$kernel->getContainer()->getParameter('cosma_testing.fixture_test_directory') . '/';
        $fixturePath .= $testClassPath;

        $fixturePaths = array();
        foreach ($fixtures as $fixture) {
            $fixturePaths[] = "{$fixturePath}/{$fixture}.yml";
        }

        return $fixturePaths;
    }

    /**
     * @param array $fixtures
     *
     * @return array
     */
    private function appendFixturesPath(array $fixtures)
    {

        $fixturePaths = array();
        foreach ($fixtures as $tableFixture) {
            $fixturePaths[] = static::getCurrentBundle()->getPath() . '/' . $tableFixture;
        }

        return $fixturePaths;
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
            $fixtureManager->persist(array(), TRUE);
        }

        $objects = $fixtureManager->loadFiles($fixtures);

        $fixtureManager->persist($objects);

        return $objects;
    }
}
