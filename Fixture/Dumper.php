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
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Cosma\Bundle\TestingBundle\Exception\InvalidEntityIdentifierException;
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
     * @param bool   $noRelations
     *
     * @return bool
     */
    public function dumpEntityToFile($entity, $noRelations = TRUE)
    {
        /** @type ClassMetadataInfo $classMetadata */
        $classMetadata = $this->entityManager->getMetadataFactory()->getMetadataFor($entity);

        $dumpData = array(
            $classMetadata->getName() => $this->dumpEntityData($entity, $noRelations)
        );

        $table = $classMetadata->getTableName();

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
     * @param bool $noRelations
     *
     * @return array
     * @throws \Cosma\Bundle\TestingBundle\Exception\InvalidEntityIdentifierException
     */
    public function dumpEntityData($entity, $noRelations = TRUE)
    {
        /** @type ClassMetadataInfo $classMetadataInfo */
        $classMetadataInfo = $this->entityManager->getMetadataFactory()->getMetadataFor($entity);

        $entities = $this->entityManager->getRepository($classMetadataInfo->getName())->findAll();

        $tableData = array();

        foreach ($entities as $entity) {
            $tableData += $this->dumpDataForOneRow($classMetadataInfo, $entity, $noRelations = TRUE);
        }

        return $tableData;
    }

    /**
     * @param       $filePath
     * @param array $dumpData
     *
     * @return mixed
     */
    private function saveYamlFile($filePath,array $dumpData)
    {
        $yamlDumper = new YamlDumper();

        $yaml = $yamlDumper->dump($dumpData, 20);

        $fileSystem = new Filesystem();

        $fileSystem->dumpFile($filePath, $yaml);

        return $filePath;
    }

    /**
     * @param \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadataInfo
     * @param                                         $entity
     * @param bool                                    $noRelations
     *
     * @return array
     * @throws \Cosma\Bundle\TestingBundle\Exception\InvalidEntityIdentifierException
     */
    private function dumpDataForOneRow(ClassMetadataInfo $classMetadataInfo, $entity, $noRelations = TRUE)
    {
        $fixtureEntityIdentifier = $this->getFixtureIdentifierForEntity( $classMetadataInfo, $entity);

        $fieldNames = $classMetadataInfo->getFieldNames();

        $dataFromRow = array();

        foreach($fieldNames as $fieldName){
            $fieldMethod = 'get'.ucfirst($fieldName);
            if(method_exists($entity, $fieldMethod)){
                $dataFromRow [$fieldName]= $this->treatFieldValue($entity->$fieldMethod());
            }else{
                throw new InvalidEntityIdentifierException("entity {$fixtureEntityIdentifier} does not have method {$fieldMethod}");
            }
        }

        return  array(
            $fixtureEntityIdentifier => $dataFromRow
        );
    }

    /**
     * @param $fieldValue
     *
     * @return string
     */
    private function treatFieldValue($fieldValue)
    {
        if($fieldValue instanceof \DateTime){
            return '<dateTimeBetween("'.$fieldValue->format('Y-m-d H:i:s').'", "'.$fieldValue->format('Y-m-d H:i:s').'")>';
        }

        return $fieldValue;
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

        foreach($identifiers as $identifier){
            $identifierMethod = 'get'.ucfirst($identifier);
            if(method_exists($entity, $identifierMethod)){
                $fixtureEntityIdentifier .= $entity->$identifierMethod();
            }else{
                throw new InvalidEntityIdentifierException("entity {$entityName} does not have identifier {$identifier}");
            }
        }

        return $fixtureEntityIdentifier;
    }
}