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
        $objects = $fixtureManager->loadFiles($fixtures);
        $fixtureManager->persist($objects);

        return $objects;
    }
}