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

use Cosma\Bundle\TestingBundle\Exception\NonExistentEntityMethodException;
use Cosma\Bundle\TestingBundle\Exception\InvalidEntityIdentifierException;

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
     * @throws \Cosma\Bundle\TestingBundle\Exception\InvalidEntityIdentifierException
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
     * @throws \Cosma\Bundle\TestingBundle\Exception\InvalidEntityIdentifierException
     * @throws \Cosma\Bundle\TestingBundle\Exception\NonExistentEntityMethodException
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
     * @throws \Cosma\Bundle\TestingBundle\Exception\InvalidEntityIdentifierException
     */
    private function getFixtureIdentifierForEntity(ClassMetadataInfo $classMetadataInfo, $entity)
    {
        $entityName = $classMetadataInfo->getName();
        $fixtureEntityIdentifier = strtolower(str_replace('\\', '_', $entityName)) . '_';

        $identifiers = $classMetadataInfo->getIdentifier();

        foreach ($identifiers as $identifier) {
            $identifierMethod = 'get' . ucfirst($identifier);
            if (method_exists($entity, $identifierMethod)) {
                $fixtureEntityIdentifier .= $entity->$identifierMethod();
            } else {
                throw new InvalidEntityIdentifierException($entityName, $identifier);
            }
        }

        return $fixtureEntityIdentifier;
    }

    /**
     * @param                                         $entity
     * @param \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadataInfo
     *
     * @return array
     * @throws \Cosma\Bundle\TestingBundle\Exception\NonExistentEntityMethodException
     */
    private function getFieldsDataForEntity($entity, ClassMetadataInfo $classMetadataInfo)
    {
        $data = array();

        $fieldNames = $classMetadataInfo->getFieldNames();

        foreach ($fieldNames as $fieldName) {
            if ($this->isGeneratedIdentity($fieldName, $classMetadataInfo)) {
                continue;
            }

            $fieldMethodName = 'get' . ucfirst($fieldName);

            if (method_exists($entity, $fieldMethodName)) {
                $data [ $fieldName ] = $this->treatFieldValueByType($entity->$fieldMethodName());
            } else {
                throw new NonExistentEntityMethodException($classMetadataInfo->getName(), $fieldMethodName);
            }
        }

        return $data;
    }

    /**
     * @param                                         $entity
     * @param \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadataInfo
     *
     * @return array
     * @throws \Cosma\Bundle\TestingBundle\Exception\NonExistentEntityMethodException
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
     * @param       $entity
     * @param array $associationMapping
     *
     * @return array
     * @throws \Cosma\Bundle\TestingBundle\Exception\NonExistentEntityMethodException
     */
    private function getDataForAssociation($entity, array $associationMapping)
    {
        $data = array();

        if ($associationMapping['isOwningSide'] > 0) {

            $associationMethodName = 'get' . ucfirst($associationMapping['fieldName']);

            if (method_exists($entity, $associationMethodName)) {

                $targetEntityClassMetadataInfo = $this->entityManager
                    ->getMetadataFactory()
                    ->getMetadataFor($associationMapping['targetEntity']);

                if (
                    ClassMetadataInfo::ONE_TO_ONE == $associationMapping['type'] ||
                    ClassMetadataInfo::MANY_TO_ONE == $associationMapping['type']
                ) {
                    $targetEntity = $entity->$associationMethodName();

                    if ($targetEntity instanceof $associationMapping['targetEntity']) {
                        $targetEntityIdentifier = $this->getFixtureIdentifierForEntity($targetEntityClassMetadataInfo, $targetEntity);
                        $data[ $associationMapping['fieldName'] ] = '@' . $targetEntityIdentifier;
                    }
                }

                if (
                    ClassMetadataInfo::ONE_TO_MANY == $associationMapping['type'] ||
                    ClassMetadataInfo::MANY_TO_MANY == $associationMapping['type']
                ) {
                    $targetEntities = $entity->$associationMethodName();
                    if (count($targetEntities)> 0) {
                        $targetEntityIdentifierCollection = array();
                        foreach ($targetEntities as $targetEntity) {
                            $targetEntityIdentifier = $this->getFixtureIdentifierForEntity($targetEntityClassMetadataInfo, $targetEntity);
                            $targetEntityIdentifierCollection[] = '@' . $targetEntityIdentifier;
                        }

                        $data[ $associationMapping['fieldName'] ] = '[ '. implode(', ', $targetEntityIdentifierCollection) .' ]';
                    }
                }

            } else {
                throw new NonExistentEntityMethodException($associationMapping['sourceEntity'], $associationMethodName);
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