<?php
/**
 * This file is part of the "cosma/testing-bundle" project
 *
 * (c) Cosmin Voicu<cosmin.voicu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 01/025/15
 * Time: 23:33
 */

namespace Cosma\Bundle\TestingBundle\Fixture;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Dumper as YamlDumper;

class Dumper
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $dumpDirectory;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $entity
     * @param bool   $associations
     *
     * @return bool
     */
    public function dumpEntityTableToFile($entity, $associations = FALSE)
    {
        /** @type ClassMetadataInfo $classMetadataInfo */
        $classMetadataInfo = $this->entityManager->getMetadataFactory()->getMetadataFor($entity);

        $dumpData = array(
            $classMetadataInfo->getName() => $this->getEntityTableData($entity, $associations)
        );

        $table = $classMetadataInfo->getTableName();

        $filePath = "{$this->dumpDirectory}/{$table}.yml";

        return $this->saveYamlFile($filePath, $dumpData);
    }

    /**
     * @return mixed
     */
    public function getDumpDirectory()
    {
        return $this->dumpDirectory;
    }

    /**
     * @param mixed $dumpDirectory
     */
    public function setDumpDirectory($dumpDirectory)
    {
        $this->dumpDirectory = $dumpDirectory;
    }

    /**
     * @param      $entity
     * @param bool $associations
     *
     * @return array
     */
    public function getEntityTableData($entity, $associations = FALSE)
    {
        /** @type ClassMetadataInfo $classMetadataInfo */
        $classMetadataInfo = $this->entityManager->getMetadataFactory()->getMetadataFor($entity);

        $entities = $this->entityManager->getRepository($classMetadataInfo->getName())->findAll();

        $tableData = array();

        foreach ($entities as $entity) {
            $tableData += $this->getDataForOneRow($classMetadataInfo, $entity, $associations);
        }

        return $tableData;
    }

    /**
     * @param       $filePath
     * @param array $dumpData
     *
     * @return mixed
     */
    private function saveYamlFile($filePath, array $dumpData)
    {
        $yamlDumper = new YamlDumper();

        $yaml = $yamlDumper->dump($dumpData, 20);

        $yaml = $this->treatYamlData($yaml);

        $fileSystem = new Filesystem();

        $fileSystem->dumpFile($filePath, $yaml);

        return $filePath;
    }

    /**
     * @param \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadataInfo
     * @param                                         $entity
     * @param bool                                    $associations
     *
     * @return array
     */
    private function getDataForOneRow(ClassMetadataInfo $classMetadataInfo, $entity, $associations = FALSE)
    {
        $fixtureEntityIdentifier = $this->getFixtureIdentifierForEntity($classMetadataInfo, $entity);

        $fieldsDataFromRow = $this->getFieldsDataForEntity($entity, $classMetadataInfo);

        $associationsDataFromRow = array();
        if ($associations) {
            $associationsDataFromRow = $this->getAssociationsDataForEntity($entity, $classMetadataInfo);
        }

        return array(
            $fixtureEntityIdentifier => $fieldsDataFromRow + $associationsDataFromRow
        );
    }

    /**
     * @param \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadataInfo
     * @param                                         $entity
     *
     * @return string
     */
    private function getFixtureIdentifierForEntity(ClassMetadataInfo $classMetadataInfo, $entity)
    {
        $entityName = $classMetadataInfo->getName();
        $fixtureEntityIdentifier = strtolower(str_replace('\\', '_', $entityName)) ;

        $identifiers = $classMetadataInfo->getIdentifier();

        foreach ($identifiers as $identifier) {
            $fixtureEntityIdentifier .= '_' . $classMetadataInfo->getFieldValue($entity, $identifier);

        }

        return $fixtureEntityIdentifier;
    }

    /**
     * @param                                         $entity
     * @param \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadataInfo
     *
     * @return array
     */
    private function getFieldsDataForEntity($entity, ClassMetadataInfo $classMetadataInfo)
    {
        $data = array();

        $fieldNames = $classMetadataInfo->getFieldNames();

        foreach ($fieldNames as $fieldName) {
            if ($this->isGeneratedIdentity($fieldName, $classMetadataInfo)) {
                continue;
            }

            $fieldValue = $classMetadataInfo->getFieldValue($entity, $fieldName);

            $data [ $fieldName ] = $this->treatFieldValueByType($fieldValue);
        }

        return $data;
    }

    /**
     * @param                                         $entity
     * @param \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadataInfo
     *
     * @return array
     */
    private function getAssociationsDataForEntity($entity, ClassMetadataInfo $classMetadataInfo)
    {
        $data = array();

        $associationMappings = $classMetadataInfo->getAssociationMappings();

        foreach ($associationMappings as $associationMapping) {

            $data += $this->getDataForAssociation($entity, $associationMapping, $classMetadataInfo);
        }

        return $data;
    }

    /**
     * @param                                         $entity
     * @param array                                   $associationMapping
     * @param \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadataInfo
     *
     * @return array
     */
    private function getDataForAssociation($entity, array $associationMapping, ClassMetadataInfo $classMetadataInfo)
    {
        $data = array();

        if ($associationMapping['isOwningSide'] > 0) {

            $targetEntityClassMetadataInfo = $this->entityManager
                ->getMetadataFactory()
                ->getMetadataFor($associationMapping['targetEntity']);

            if (
                ClassMetadataInfo::ONE_TO_ONE == $associationMapping['type'] ||
                ClassMetadataInfo::MANY_TO_ONE == $associationMapping['type']
            ) {
                $targetEntity = $classMetadataInfo->getFieldValue($entity, $associationMapping['fieldName']);

                if ($targetEntity instanceof $associationMapping['targetEntity']) {
                    $targetEntityIdentifier = $this->getFixtureIdentifierForEntity($targetEntityClassMetadataInfo, $targetEntity);
                    $data[ $associationMapping['fieldName'] ] = '@' . $targetEntityIdentifier;
                }
            }

            if (
                ClassMetadataInfo::ONE_TO_MANY == $associationMapping['type'] ||
                ClassMetadataInfo::MANY_TO_MANY == $associationMapping['type']
            ) {
                $targetEntities = $classMetadataInfo->getFieldValue($entity, $associationMapping['fieldName']);
                if (count($targetEntities) > 0) {
                    $targetEntityIdentifierCollection = array();
                    foreach ($targetEntities as $targetEntity) {
                        $targetEntityIdentifier = $this->getFixtureIdentifierForEntity($targetEntityClassMetadataInfo, $targetEntity);
                        $targetEntityIdentifierCollection[] = '@' . $targetEntityIdentifier;
                    }

                    $data[ $associationMapping['fieldName'] ] = '[ ' . implode(', ', $targetEntityIdentifierCollection) . ' ]';
                }
            }
        }

        return $data;
    }

    /**
     * @param string                                  $fieldName
     * @param \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadataInfo
     *
     * @return bool
     */
    private
    function isGeneratedIdentity($fieldName, ClassMetadataInfo $classMetadataInfo)
    {
        return ($classMetadataInfo->isIdGeneratorIdentity() &&
            $classMetadataInfo->isIdentifier($fieldName));
    }

    /**
     * @param $fieldValue
     *
     * @return string
     */
    private function treatFieldValueByType($fieldValue)
    {
        /**
         * DateTime for fzaninotto/Faker format
         */
        if ($fieldValue instanceof \DateTime) {
            return '<dateTimeBetween("' . $fieldValue->format('Y-m-d H:i:s') . '", "' . $fieldValue->format('Y-m-d H:i:s') . '")>';
        }

        return $fieldValue;
    }

    private function treatYamlData($yamlData)
    {
        /**
         * strip quotes for associative collection
         */
        $yamlData = str_replace(array(": '[ ", " ]'"), array(": [ ", " ]"), $yamlData);

        return $yamlData;
    }
}