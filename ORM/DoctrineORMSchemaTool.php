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

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Connection;
use \Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\Tools\SchemaTool as DoctrineSchemaTool;
use h4cc\AliceFixturesBundle\ORM\DoctrineORMSchemaTool as DoctrineORMSchemaToolBase;
class DoctrineORMSchemaTool extends DoctrineORMSchemaToolBase
{
    const DOCTRINE_CLEANING_TRUNCATE = 'truncate';
    const DOCTRINE_CLEANING_DROP     = 'drop';
    /**
     * @type string
     */
    private $doctrineMigrationsTable = null;
    /**
     * {@inheritDoc}
     */
    public function dropSchema()
    {
        $this->foreachObjectManagers(function () {
            /** @type Connection $connection */
            $connection = $this->managerRegistry->getConnection();
            $connection->beginTransaction();
            try {
                $connection->query('SET FOREIGN_KEY_CHECKS=0');
                /** @var Table $table */
                foreach ($connection->getSchemaManager()->listTables() as $table) {
                    if ($this->doctrineMigrationsTable == $table->getName()) {
                        continue;
                    }
                    $sql = "TRUNCATE `{$table->getName()}`";
                    $connection->exec($sql);
                }
                $connection->query('SET FOREIGN_KEY_CHECKS=1');
                $connection->commit();
            } catch (\Exception $e) {
                $connection->rollback();
                throw $e;
            }
        });
    }
    /**
     * {@inheritDoc}
     */
    public function createSchema()
    {
        $this->foreachObjectManagers(function (ObjectManager $objectManager) {
            /** @type Connection $connection */
            $connection = $this->managerRegistry->getConnection();
            $tableNames = $connection->getSchemaManager()->listTableNames();
            $missingTablesMetaData = [];
            /** @var ClassMetadata $classMetadata */
            foreach ($objectManager->getMetadataFactory()->getAllMetadata() as $classMetadata) {
                if (!in_array($classMetadata->table['name'], $tableNames)) {
                    $missingTablesMetaData[] = $classMetadata;
                }
            }
            if (count($missingTablesMetaData) > 0) {
                $schemaTool = new DoctrineSchemaTool($objectManager);
                $schemaTool->createSchema($missingTablesMetaData);
            }
        });
    }
    private function foreachObjectManagers($callback)
    {
        array_map($callback, $this->managerRegistry->getManagers());
    }
    /**
     * @return string
     */
    public function getDoctrineMigrationsTable()
    {
        return $this->doctrineMigrationsTable;
    }
    /**
     * @param string $doctrineMigrationsTable
     *
     * @return $this
     */
    public function setDoctrineMigrationsTable($doctrineMigrationsTable)
    {
        $this->doctrineMigrationsTable = $doctrineMigrationsTable;
        return $this;
    }
}
