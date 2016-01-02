<?php
/**
 * This file is part of the TestingBundle project
 *
 * (c) Cosmin Voicu<cosmin.voicu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 14/12/15
 * Time: 18:30
 */

namespace Cosma\Bundle\TestingBundle\TestCase\Traits;

use Doctrine\ORM\EntityManager;
use h4cc\AliceFixturesBundle\Fixtures\FixtureManager;

trait DBTestTrait
{
    /**
     * @var FixtureManager
     */
    protected $fixtureManager;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->getFixtureManager();
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getKernel()->getContainer()->get('doctrine.orm.entity_manager');
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
     * void
     */
    protected function dropDatabase()
    {
        $this->fixtureManager->persist([], true);
    }

    /**
     * @return FixtureManager
     */
    protected function getFixtureManager()
    {
        if (null === $this->fixtureManager) {
            $this->fixtureManager = $this->getKernel()->getContainer()->get('h4cc_alice_fixtures.manager');
        }

        return $this->fixtureManager;
    }

    /**
     * @param array $fixtures
     * @param       $dropDatabaseBefore
     *
     * @return array
     */
    protected function loadFixtures(array $fixtures, $dropDatabaseBefore = true)
    {
        if ($dropDatabaseBefore) {
            $this->fixtureManager->persist([], true);
        }

        $fixturesFiles = array_map([$this, 'getFixtureFile'], $fixtures);

        $objects = $this->fixtureManager->loadFiles($fixturesFiles);
        $this->fixtureManager->persist($objects);

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
        if (!is_array($pathParts) || count($pathParts) < 2) {
            throw new \Exception("You are trying to load a wrong fixture - {$fixture}");
        }

        $bundleName = array_shift($pathParts);

        $bundle = $this->getKernel()->getBundle($bundleName);

        $fixtureDirectory = $this->getKernel()->getContainer()->getParameter('cosma_testing.fixture_directory');

        $innerBundlePath = implode(DIRECTORY_SEPARATOR, $pathParts);

        return $bundle->getPath() . DIRECTORY_SEPARATOR . $fixtureDirectory . DIRECTORY_SEPARATOR . $innerBundlePath . '.yml';
    }
}