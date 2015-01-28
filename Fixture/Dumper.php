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
     * @var ClassMetadataInfo
     */
    private $entityClassMetadataInfo;


    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
     * @param string $entityName
     * @param bool $associations
     *
     * @return bool
     */
    public function dumpDataToYamlFile($entityName, $associations = FALSE)
    {
        /** @type ClassMetadataInfo $classMetadataInfo */
        $classMetadataInfo = $this->entityManager->getMetadataFactory()->getMetadataFor($entityName);

        $dumpData = array(
            $classMetadataInfo->getName() => $this->getData($entityName, $associations)
        );

        $table = $classMetadataInfo->getTableName();

        $filePath = "{$this->dumpDirectory}/{$table}.yml";

        return $this->saveYamlFile($filePath, $dumpData);
    }

    /**
     * @param      $entityName
     * @param bool $associations
     *
     * @return array
     */
    public function getData($entityName, $associations = FALSE)
    {
        /** @type ClassMetadataInfo $classMetadataInfo */
        $classMetadataInfo = $this->entityManager->getMetadataFactory()->getMetadataFor($entityName);

        $entities = $this->entityManager->getRepository($classMetadataInfo->getName())->findAll();

        $tableData = array();

        foreach ($entities as $entity) {
            $tableData += $this->getDataForEntity($classMetadataInfo, $entity, $associations);
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
     * @param object                                  $entity
     * @param bool $associations
     *
     * @return array
     */
    private function getDataForEntity(ClassMetadataInfo $classMetadataInfo, $entity, $associations = FALSE)
    {
        $fixtureEntityIdentifier = $this->getIdentifierForEntity($classMetadataInfo, $entity);

        $fieldsDataFromRow = $this->getFieldsDataForEntity($entity, $classMetadataInfo);

        $associationsDataFromRow = array();
        if ($associations) {
            $associationsDataFromRow = $this->getOwningAssociationsDataForEntity($entity, $classMetadataInfo);
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
    private function getIdentifierForEntity(ClassMetadataInfo $classMetadataInfo, $entity)
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
     * @param object                                  $entity
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

            $data [$fieldName] = $this->treatFieldValueByType($fieldValue);
        }

        return $data;
    }

    /**
     * @param object                                  $entity
     * @param \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadataInfo
     *
     * @return array
     */
    private function getOwningAssociationsDataForEntity($entity, ClassMetadataInfo $classMetadataInfo)
    {
        $data = array();

        $associationMappings = $classMetadataInfo->getAssociationMappings();

        foreach ($associationMappings as $associationMapping) {
            if ($associationMapping['isOwningSide'] > 0) {
                $data[$associationMapping['fieldName']] = $this->getTargetIdentifier($entity, $associationMapping, $classMetadataInfo);
            }
        }

        return $data;
    }

    /**
     * @param object $entity
     * @param array $associationMapping
     * @param ClassMetadataInfo $classMetadataInfo
     *
     * @return null|string
     */
    private function getTargetIdentifier($entity, array $associationMapping, ClassMetadataInfo $classMetadataInfo)
    {
        $targetIdentifier = NULL;
        if ($this->isSingleTargetedAssociation($associationMapping)) {

            $targetIdentifier = $this->getSingleTargetAssociationIdentifier($entity, $associationMapping, $classMetadataInfo);

        } elseif ($this->isMultiTargetedAssociation($associationMapping)) {

            $targetIdentifier = $this->getMultiTargetAssociationIdentifier($entity, $associationMapping, $classMetadataInfo);
        }
        return $targetIdentifier;
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
     * @param ClassMetadataInfo $classMetadataInfo
     *
     * @return null|string
     */
    private function getSingleTargetAssociationIdentifier($entity, array $associationMapping, ClassMetadataInfo $classMetadataInfo)
    {
        $targetIdentifier = null;

        $targetEntity = $classMetadataInfo->getFieldValue($entity, $associationMapping['fieldName']);

        if ($targetEntity instanceof $associationMapping['targetEntity']) {

            $targetClassMetadataInfo = $this->entityManager
                ->getMetadataFactory()
                ->getMetadataFor($associationMapping['targetEntity']);

            $targetIdentifier = '@' . $this->getIdentifierForEntity($targetClassMetadataInfo, $targetEntity);
        }
        return $targetIdentifier;
    }

    /**
     * @param object $entity
     * @param array $associationMapping
     * @param ClassMetadataInfo $classMetadataInfo
     *
     * @return null|string
     */
    private function getMultiTargetAssociationIdentifier($entity, array $associationMapping, ClassMetadataInfo $classMetadataInfo)
    {
        $targetIdentifier = NULL;

        $targetEntities = $classMetadataInfo->getFieldValue($entity, $associationMapping['fieldName']);
        if (count($targetEntities) > 0) {

            $targetClassMetadataInfo = $this->entityManager
                ->getMetadataFactory()
                ->getMetadataFor($associationMapping['targetEntity']);

            $targetEntityIdentifierCollection = array();
            foreach ($targetEntities as $targetEntity) {
                $targetEntityIdentifier = $this->getIdentifierForEntity($targetClassMetadataInfo, $targetEntity);
                $targetEntityIdentifierCollection[] = '@' . $targetEntityIdentifier;
            }

            $targetIdentifier = '[ ' . implode(', ', $targetEntityIdentifierCollection) . ' ]';
        }
        return $targetIdentifier;
    }
}