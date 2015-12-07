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

use Doctrine\DBAL\Schema\Table;
use \Doctrine\ORM\Tools\SchemaTool as SchemaToolBase;

class SchemaTool extends SchemaToolBase
{

    /**
     * @type string
     */
    private $doctrineMigrationsTable = null;

    /**
     * create only missing tables.
     *
     * {@inheritDoc}
     */
    public function createSchema(array $classes)
    {
        echo "createSchema method";

        parent::createSchema($classes);
//        $connection = $this->entityManager->getConnection();
//        $tableNames = $connection->getSchemaManager()->listTableNames();
//
//        $missingTablesMetaData = [];
//
//        /** @var ClassMetadata $classMetadata */
//        foreach ($this->entityManager->getMetadataFactory()->getAllMetadata() as $classMetadata) {
//            if (!in_array($classMetadata->table['name'], $tableNames)) {
//                $missingTablesMetaData[] = $classMetadata;
//            }
//        }
//
//        if (count($missingTablesMetaData) > 0) {
//            $this->doctrineSchemaTool->createSchema($missingTablesMetaData);
//        }
    }


//    /**
//     * Drops all elements in the database of the current connection.
//     *
//     * @return void
//     */
//    public function dropDatabase()
//    {
//        $dropSchemaSql = $this->getDropDatabaseSQL();
//        $conn = $this->em->getConnection();
//
//        foreach ($dropSchemaSql as $sql) {
//            $conn->executeQuery($sql);
//        }
//    }

    /**
     * truncate instead of drop.
     */
    public function truncateTables(array $classes)
    {
        echo "truncateTables method";

//        $connection = $this->em->getConnection();
//
//        $connection->beginTransaction();
//        try {
//            $connection->query('SET FOREIGN_KEY_CHECKS=0');
//
//            /** @var Table $table */
//            foreach ($connection->getSchemaManager()->listTableNames() as $tableName) {
//                if ($this->doctrineMigrationsTable == $tableName) {
//                    continue;
//                }
//
//                $truncateSql = "TRUNCATE `{$tableName}`";
//                $connection->exec($truncateSql);
//            }
//
//            $connection->query('SET FOREIGN_KEY_CHECKS=1');
//            $connection->commit();
//        } catch (\Exception $e) {
//            $connection->rollback();
//            throw $e;
//        }
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
