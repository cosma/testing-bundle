<?php
/**
 * This file is part of the TestingBundle project
 *
 * (c) Cosmin Voicu<cosmin.voicu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 18/10/15
 * Time: 18:24
 */

namespace Cosma\Bundle\TestingBundle\TestCase\Traits;

use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Client;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

trait WebTestTrait
{
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
     * @return void
     */
    protected function tearDown()
    {
        parent::tearDown();
        \Mockery::close();
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

        /** @var EntityManager $entityManager */
        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();

        /** @var ClassMetadataFactory $metadataFactory */
        $metadataFactory = $entityManager->getMetadataFactory();

        if (FALSE !== strpos($entity, ':')) {
            $entityDescription = explode(':', $entity);

            $bundleName = $entityDescription[0];
            $entityName = $entityDescription[1];

            $bundle = $this->getBundleByName($bundleName);

            $fullPathEntity = $this->getEntityNamespaceForBundle($bundle, $metadataFactory) .
                              '\\' .
                              $entityName;

            return $fullPathEntity;
        }

        $fullPathEntity = $this->getEntityNamespaceForBundle(static::getCurrentBundle(), $metadataFactory) .
                          '\\' .
                          $entity;

        return $fullPathEntity;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Bundle\BundleInterface      $bundle
     * @param \Doctrine\Common\Persistence\Mapping\ClassMetadataFactory $metadataFactory
     *
     * @return string
     */
    private function getEntityNamespaceForBundle(BundleInterface $bundle,ClassMetadataFactory $metadataFactory)
    {

        $metadataCollection = $metadataFactory->getAllMetadata();

        /** @var ClassMetadata $metadata */
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
    private function appendCustomFixturesPath(array $fixtures)
    {

        $fixturePaths = array();
        foreach ($fixtures as $tableFixture) {
            $fixturePaths[] = static::getCurrentBundle()->getPath() . '/' . $tableFixture.'.yml';
        }

        return $fixturePaths;
    }
}