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
     * @var ClassMetadataInfo
     */
    private $classMetadataInfo;

    /**
     * @var bool
     */
    private $association = FALSE;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return ClassMetadataInfo
     */
    public function getClassMetadataInfo()
    {
        return $this->classMetadataInfo;
    }

    /**
     * @param ClassMetadataInfo $classMetadataInfo
     */
    public function setClassMetadataInfo($classMetadataInfo)
    {
        $this->classMetadataInfo = $classMetadataInfo;
    }

    /**
     * @return boolean
     */
    public function isAssociation()
    {
        return $this->association;
    }

    /**
     * @param boolean $association
     */
    public function setAssociation($association)
    {
        $this->association = $association;
    }

    /**
     * @param string $dumpDirectory
     *
     * @return bool
     */
    public function dumpToYaml($dumpDirectory)
    {
        $dumpData = array(
            $this->classMetadataInfo->getName() => $this->getData()
        );

        $tableName = $this->classMetadataInfo->getTableName();

        $filePath = "{$dumpDirectory}/{$tableName}.yml";

        return $this->saveYamlFile($filePath, $dumpData);
    }

    /**
     * get data for all entities
     *
     * @return array
     */
    public function getData()
    {
        $entities = $this->entityManager->getRepository($this->classMetadataInfo->getName())->findAll();

        $tableData = array();

        foreach ($entities as $entity) {
            $tableData += $this->getDataForEntity($entity);
        }

        return $tableData;
    }

    /**
     * @param object $entity
     *
     * @return array
     */
    private function getDataForEntity($entity)
    {
        $fixtureEntityIdentifier = $this->getIdentifierForEntity($entity, $this->classMetadataInfo);

        $fieldsDataFromRow = $this->getFieldsDataForEntity($entity);

        $associationsDataFromRow = array();
        if ($this->isAssociation()) {
            $associationsDataFromRow = $this->getOwningAssociationsDataForEntity($entity);
        }

        return array(
            $fixtureEntityIdentifier => $fieldsDataFromRow + $associationsDataFromRow
        );
    }

    /**
     * @param object $entity
     *
     * @return array
     */
    private function getFieldsDataForEntity($entity)
    {
        $data = array();

        $fieldNames = $this->classMetadataInfo->getFieldNames();

        foreach ($fieldNames as $fieldName) {
            if ($this->isGeneratedIdentity($fieldName, $this->classMetadataInfo)) {
                continue;
            }

            $data [$fieldName] = $this->getFieldValueFromEntity($fieldName, $entity);
        }

        return $data;
    }

    /**
     * @param string $fieldName
     * @param object $entity
     *
     * @return mixed|string
     */
    private function getFieldValueFromEntity($fieldName, $entity)
    {
        $fieldValue = $this->classMetadataInfo->getFieldValue($entity, $fieldName);

        /**
         * DateTime for fzaninotto/Faker format
         */
        if ($fieldValue instanceof \DateTime) {
            $fieldValue = '<dateTimeBetween("' . $fieldValue->format('Y-m-d H:i:s') . '", "' . $fieldValue->format('Y-m-d H:i:s') . '")>';
        }

        return $fieldValue;
    }

    /**
     * @param object $entity
     *
     * @return array
     */
    private function getOwningAssociationsDataForEntity($entity)
    {
        $data = array();

        $associationMappings = $this->classMetadataInfo->getAssociationMappings();

        foreach ($associationMappings as $associationMapping) {
            if ($associationMapping['isOwningSide'] > 0) {
                $targetAssociationIdentifier = $this->getTargetAssociationIdentifier($entity, $associationMapping);
                if ($targetAssociationIdentifier) {
                    $data[$associationMapping['fieldName']] = $targetAssociationIdentifier;
                }
            }
        }

        return $data;
    }

    /**
     * @param object $entity
     * @param array $associationMapping
     *
     * @return null|string
     */
    private function getTargetAssociationIdentifier($entity, array $associationMapping)
    {
        $targetIdentifier = NULL;
        if ($this->isSingleTargetedAssociation($associationMapping)) {

            $targetIdentifier = $this->getSingleTargetAssociationIdentifier($entity, $associationMapping);

        } elseif ($this->isMultiTargetedAssociation($associationMapping)) {

            $targetIdentifier = $this->getMultiTargetAssociationIdentifier($entity, $associationMapping);
        }
        return $targetIdentifier;
    }

    /**
     * @param array $associationMapping
     *
     * @return bool
     */
    private function isSingleTargetedAssociation(array $associationMapping)
    {
        return ClassMetadataInfo::ONE_TO_ONE == $associationMapping['type'] ||
        ClassMetadataInfo::MANY_TO_ONE == $associationMapping['type'];
    }

    /**
     * @param array $associationMapping
     *
     * @return bool
     */
    private function isMultiTargetedAssociation(array $associationMapping)
    {
        return ClassMetadataInfo::ONE_TO_MANY == $associationMapping['type'] ||
        ClassMetadataInfo::MANY_TO_MANY == $associationMapping['type'];
    }

    /**
     * @param object $entity
     * @param array $associationMapping
     *
     * @return null|string
     */
    private function getSingleTargetAssociationIdentifier($entity, array $associationMapping)
    {
        $targetIdentifier = null;

        $targetEntity = $this->classMetadataInfo->getFieldValue($entity, $associationMapping['fieldName']);

        if ($targetEntity instanceof $associationMapping['targetEntity']) {

            $targetClassMetadataInfo = $this->entityManager
                ->getMetadataFactory()
                ->getMetadataFor($associationMapping['targetEntity']);

            $targetIdentifier = '@' . $this->getIdentifierForEntity($targetEntity, $targetClassMetadataInfo);
        }
        return $targetIdentifier;
    }

    /**
     * @param object $entity
     * @param array $associationMapping
     *
     * @return null|string
     */
    private function getMultiTargetAssociationIdentifier($entity, array $associationMapping)
    {
        $targetIdentifier = NULL;

        $targetEntities = $this->classMetadataInfo->getFieldValue($entity, $associationMapping['fieldName']);
        if (count($targetEntities) > 0) {

            $targetClassMetadataInfo = $this->entityManager
                ->getMetadataFactory()
                ->getMetadataFor($associationMapping['targetEntity']);

            $targetEntityIdentifierCollection = array();
            foreach ($targetEntities as $targetEntity) {
                $targetEntityIdentifier = $this->getIdentifierForEntity($targetEntity, $targetClassMetadataInfo);
                $targetEntityIdentifierCollection[] = '@' . $targetEntityIdentifier;
            }

            $targetIdentifier = '[ ' . implode(', ', $targetEntityIdentifierCollection) . ' ]';
        }
        return $targetIdentifier;
    }

    /**
     * @param                                         $entity
     * @param \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadataInfo
     *
     * @return string
     */
    private function getIdentifierForEntity($entity, ClassMetadataInfo $classMetadataInfo)
    {
        $entityName = $classMetadataInfo->getName();
        $fixtureEntityIdentifier = strtolower(str_replace('\\', '_', $entityName));

        $identifiers = $classMetadataInfo->getIdentifier();

        foreach ($identifiers as $identifier) {
            $fixtureEntityIdentifier .= '_' . $classMetadataInfo->getFieldValue($entity, $identifier);

        }

        return $fixtureEntityIdentifier;
    }

    /**
     * @param string $fieldName
     * @param \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadataInfo
     *
     * @return bool
     */
    private function isGeneratedIdentity($fieldName, ClassMetadataInfo $classMetadataInfo)
    {
        return ($classMetadataInfo->isIdGeneratorIdentity() &&
            $classMetadataInfo->isIdentifier($fieldName));
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
     * @param string $yamlData
     *
     * @return string
     */
    private function treatYamlData($yamlData)
    {
        /**
         * strip quotes for associative collection
         */
        $yamlData = str_replace(array(": '[ ", " ]'"), array(": [ ", " ]"), $yamlData);

        return $yamlData;
    }
}