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
use Hautelook\AliceBundle\Alice\DataFixtures\Loader;

trait DBTestTrait
{

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
        //hautelook_alice.alice.fixtures.loader
        //$this->get

        /** @type Loader $loader */
        //$loader = static::$kernel->getContainer('hautelook_alice.alice.fixtures.loader');

        //$loader->load(, $fixtures);



//        $fixtureManager = static::getFixtureManager();
//        if ($dropDatabaseBefore) {
//            $fixtureManager->persist(array(), true);
//        }
//
//        $objects = $fixtureManager->loadFiles($fixtures);
//
//        $fixtureManager->persist($objects);
//
//        return $objects;
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


    
}