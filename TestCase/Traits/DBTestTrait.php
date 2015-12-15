<?php
/**
 * This file is part of the TestingBundle project
 *
 * (c) Cosmin Voicu<cosmin.voicu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 11/12/15
 * Time: 18:30
 */

namespace Cosma\Bundle\TestingBundle\TestCase\Traits;

use Doctrine\ORM\EntityManager;
use h4cc\AliceFixturesBundle\Fixtures\FixtureManager;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

trait DBTestTrait
{
    /**
     * @var FixtureManager
     */
    private static $fixtureManager;

    /**
     * @return void
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::getFixtureManager();
    }

    /**
     * Clean up Kernel usage in this test.
     */
    public static function tearDownAfterClass()
    {
        self::$fixtureManager = null;

        parent::tearDownAfterClass();
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
        return $this->getEntityManager()->getRepository($entityName);
    }

    /**
     * @param array $fixtures
     * @param       $dropDatabaseBefore
     *
     * @return array
     */
    protected function loadFixtures(array $fixtures, $dropDatabaseBefore = true)
    {
        $fixtureManager = static::getFixtureManager();
        if ($dropDatabaseBefore) {
            $fixtureManager->persist([], true);
        }

        $fixturesFiles = array_map([$this, 'getFixtureFile'], $fixtures);

        $objects = $fixtureManager->loadFiles($fixturesFiles);
        $fixtureManager->persist($objects);

        return $objects;
    }

    /**
     * @param $fixture
     *
     * @return string
     * @throws \Exception
     */
    private function getFixtureFile($fixture)
    {
        $pathParts = explode(':', $fixture);
        if (!is_array($pathParts) && count($pathParts) < 2) {
            throw new \Exception("You are trying to load a wrong fixture - {$fixture}");
        }

        $bundleName = array_shift($pathParts);

        $bundlePath = $this->getBundlePath($bundleName);

        $fixtureDirectory = static::$kernel->getContainer()->getParameter('cosma_testing.fixture_directory');

        $innerBundlePath = implode(DIRECTORY_SEPARATOR, $pathParts);

        return $bundlePath . DIRECTORY_SEPARATOR . $fixtureDirectory . DIRECTORY_SEPARATOR . $innerBundlePath . '.yml';
    }

    /**
     * @return FixtureManager
     */
    private static function getFixtureManager()
    {
        if (null === self::$fixtureManager) {
            self::$fixtureManager = static::$kernel->getContainer()->get('h4cc_alice_fixtures.manager');
        }

        return self::$fixtureManager;
    }

    /**
     * @param $bundleName
     *
     * @return string
     * @throws \Exception
     */
    private function getBundlePath($bundleName)
    {
        $bundles = static::$kernel->getBundles();

        /** @type BundleInterface $bundle */
        foreach ($bundles as $bundle) {
            if ($bundleName == $bundle->getName()) {
                return $bundle->getPath();
            }
        }

        throw new \Exception("This bundle {$bundleName} doesn't exists");
    }
}