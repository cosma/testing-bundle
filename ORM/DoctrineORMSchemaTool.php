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

namespace Cosma\Bundle\TestingBundle\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Cosma\Bundle\TestingBundle\ORM\SchemaTool as DoctrineSchemaTool;
use h4cc\AliceFixturesBundle\ORM\DoctrineORMSchemaTool as DoctrineORMSchemaToolBase;


class DoctrineORMSchemaTool extends DoctrineORMSchemaToolBase
{

    const DOCTRINE_CLEANING_TRUNCATE = 'truncate';
    const DOCTRINE_CLEANING_DROP     = 'drop';


    /**
     * {@inheritDoc}
     */
    public function dropSchema()
    {
        $this->foreachObjectManagers(function(ObjectManager $objectManager) {
            $metadata = $objectManager->getMetadataFactory()->getAllMetadata();

            print_r($metadata);

            $schemaTool = new DoctrineSchemaTool($objectManager);
            $schemaTool->truncateTables($metadata);


        });
    }

    /**
     * {@inheritDoc}
     */
    public function createSchema()
    {
        $this->foreachObjectManagers(function(ObjectManager $objectManager) {
            $metadata = $objectManager->getMetadataFactory()->getAllMetadata();

            $schemaTool = new DoctrineSchemaTool($objectManager);
            $schemaTool->createSchema($metadata);
        });
    }

    private function foreachObjectManagers($callback)
    {
        array_map($callback, $this->managerRegistry->getManagers());
    }
}
