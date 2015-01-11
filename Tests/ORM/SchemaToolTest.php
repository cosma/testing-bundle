<?php
/**
 * This file is part of the TestingBundle project.
 *
 * @project    TestingBundle
 * @author     Cosmin Voicu <cosmin.voicu@crobo.com>
 * @copyright  2015 - Crobo GmbH
 * @license    http://crobo.com proprietary
 * @link       http://crobo.com
 */

namespace Cosma\Bundle\TestingBundle\Tests\ORM;


use Cosma\Bundle\TestingBundle\ORM\SchemaTool;

class SchemaToolTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @see Cosma\Bundle\TestingBundle\ORM\SchemaTool::createSchema
     */
    public function testCreateSchema()
    {
        $schemaManager = $this->getMockBuilder('\Doctrine\DBAL\Schema\AbstractSchemaManager')
            ->disableOriginalConstructor()
            ->setMethods(array('listTableNames', 'createSchemaConfig'))
            ->getMockForAbstractClass();
        $schemaManager->expects($this->once())
            ->method('listTableNames')
            ->will($this->returnValue(array('table_example')));

        $connection = $this->getMockBuilder('\Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->setMethods(array('getSchemaManager'))
            ->getMock();
        $connection->expects($this->once())
            ->method('getSchemaManager')
            ->will($this->returnValue($schemaManager));

        $metaDataOne = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->getMock();

        $metaDataTwo = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->getMock();

        $metaDataFactory = $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadataFactory')
            ->disableOriginalConstructor()
            ->setMethods(array('getAllMetadata'))
            ->getMock();
        $metaDataFactory->expects($this->once())
            ->method('getAllMetadata')
            ->will($this->returnValue(array($metaDataOne, $metaDataTwo)));

        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getConnection', 'getMetadataFactory'))
            ->getMock();
        $entityManager->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($connection));
        $entityManager->expects($this->once())
            ->method('getMetadataFactory')
            ->will($this->returnValue($metaDataFactory));

        $schemaTool = $this->getMockBuilder('Doctrine\ORM\Tools\SchemaTool')
            ->disableOriginalConstructor()
            ->setMethods(array('createSchema'))
            ->getMockForAbstractClass();
        $schemaTool->expects($this->once())
            ->method('createSchema')
            ->with(array($metaDataOne, $metaDataTwo))
            ->will($this->returnValue(null));

        /** @var SchemaTool $mockedSchemaTool */
        $mockedSchemaTool = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\ORM\SchemaTool')
            ->disableOriginalConstructor()
            ->setMethods(array('dropSchema'))
            ->getMock();

        $reflectionClassMocked = new \ReflectionClass($mockedSchemaTool);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $entityManagerProperty = $reflectionClass->getProperty('entityManager');
        $entityManagerProperty->setAccessible(TRUE);
        $entityManagerProperty->setValue($mockedSchemaTool, $entityManager);

        $schemaToolProperty = $reflectionClass->getProperty('doctrineSchemaTool');
        $schemaToolProperty->setAccessible(TRUE);
        $schemaToolProperty->setValue($mockedSchemaTool, $schemaTool);

        $mockedSchemaTool->createSchema();
    }

    /**
     * @see Cosma\Bundle\TestingBundle\ORM\SchemaTool::dropSchema
     */
    public function testDropSchema()
    {
        $schemaManager = $this->getMockBuilder('\Doctrine\DBAL\Schema\AbstractSchemaManager')
            ->disableOriginalConstructor()
            ->setMethods(array('listTableNames', 'createSchemaConfig'))
            ->getMockForAbstractClass();
        $schemaManager->expects($this->once())
            ->method('listTableNames')
            ->will($this->returnValue(array('table_example')));

        $connection = $this->getMockBuilder('\Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->setMethods(array('getSchemaManager', 'connect', 'beginTransaction', 'query', 'exec', 'commit', 'rollback'))
            ->getMock();
        $connection->expects($this->once())
            ->method('getSchemaManager')
            ->will($this->returnValue($schemaManager));
        $connection->expects($this->once())
            ->method('exec')
            ->with('TRUNCATE `table_example`')
            ->will($this->returnValue(true));

        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getConnection'))
            ->getMock();
        $entityManager->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($connection));

        /** @var SchemaTool $mockedSchemaTool */
        $mockedSchemaTool = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\ORM\SchemaTool')
            ->disableOriginalConstructor()
            ->setMethods(array('createSchema'))
            ->getMock();

        $reflectionClassMocked = new \ReflectionClass($mockedSchemaTool);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $entityManagerProperty = $reflectionClass->getProperty('entityManager');
        $entityManagerProperty->setAccessible(TRUE);
        $entityManagerProperty->setValue($mockedSchemaTool, $entityManager);

        $mockedSchemaTool->dropSchema();
    }
}