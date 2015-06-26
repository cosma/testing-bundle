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
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use h4cc\AliceFixturesBundle\ORM\DoctrineORMSchemaTool;

class SchemaTool extends DoctrineORMSchemaTool
{
    const DOCTRINE_CLEANING_TRUNCATE = 'truncate';
    const DOCTRINE_CLEANING_DROP     = 'drop';

    /**
     * @type string
     */
    private $doctrineMigrationsTable = null;

    /**
     * create only missing tables.
     *
     * {@inheritDoc}
     */
    public function createSchema()
    {
        $connection = $this->entityManager->getConnection();
        $tableNames = $connection->getSchemaManager()->listTableNames();

        $missingTablesMetaData = [];

        /** @var ClassMetadata $classMetadata */
        foreach ($this->entityManager->getMetadataFactory()->getAllMetadata() as $classMetadata) {
            if (!in_array($classMetadata->table['name'], $tableNames)) {
                $missingTablesMetaData[] = $classMetadata;
            }
        }

        if (count($missingTablesMetaData) > 0) {
            $this->doctrineSchemaTool->createSchema($missingTablesMetaData);
        }
    }

    /**
     * truncate instead of drop.
     */
    public function dropSchema()
    {
        $connection = $this->entityManager->getConnection();

        $connection->beginTransaction();
        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');

            /** @var Table $table */
            foreach ($connection->getSchemaManager()->listTableNames() as $tableName) {
                if ($this->doctrineMigrationsTable == $tableName) {
                    continue;
                }

                $truncateSql = "TRUNCATE `{$tableName}`";
                $connection->exec($truncateSql);
            }

            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
            throw $e;
        }
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
