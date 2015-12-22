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

namespace Cosma\Bundle\TestingBundle\Tests\ORM;

use Cosma\Bundle\TestingBundle\ORM\DoctrineORMSchemaTool;

class DoctrineORMSchemaToolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see Cosma\Bundle\TestingBundle\ORM\DoctrineORMSchemaTool::dropSchema
     */
    public function testDropSchema_DoctrineMigrations()
    {
        $schemaManager = $this->getMockBuilder('\Doctrine\DBAL\Schema\AbstractSchemaManager')
                              ->disableOriginalConstructor()
                              ->setMethods(['listTableNames', 'createSchemaConfig'])
                              ->getMockForAbstractClass()
        ;
        $schemaManager->expects($this->once())
                      ->method('listTableNames')
                      ->will($this->returnValue(['doctrine_migrations_table']))
        ;

        $connection = $this->getMockBuilder('\Doctrine\DBAL\Connection')
                           ->disableOriginalConstructor()
                           ->setMethods(['getSchemaManager', 'connect', 'beginTransaction', 'query', 'exec', 'commit', 'rollback'])
                           ->getMock()
        ;
        $connection->expects($this->once())
                   ->method('getSchemaManager')
                   ->will($this->returnValue($schemaManager))
        ;
        $connection->expects($this->never())
                   ->method('exec')
                   ->will($this->returnValue(true))
        ;

        $managerRegistry = $this->getMockBuilder('Doctrine\Common\Persistence\ManagerRegistry')
                                ->disableOriginalConstructor()
                                ->setMethods(['getConnection', 'getManagers'])
                                ->getMockForAbstractClass()
        ;
        $managerRegistry->expects($this->once())
                        ->method('getConnection')
                        ->will($this->returnValue($connection))
        ;

        /** @var DoctrineORMSchemaTool $mockedSchemaTool */
        $mockedSchemaTool = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\ORM\DoctrineORMSchemaTool')
                                 ->disableOriginalConstructor()
                                 ->setMethods(['createSchema'])
                                 ->getMock()
        ;

        $reflectionClassMocked = new \ReflectionClass($mockedSchemaTool);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $managerRegistryProperty = $reflectionClass->getProperty('managerRegistry');
        $managerRegistryProperty->setAccessible(true);
        $managerRegistryProperty->setValue($mockedSchemaTool, $managerRegistry);

        $mockedSchemaTool->setDoctrineMigrationsTable('doctrine_migrations_table');

        $mockedSchemaTool->dropSchema();
    }

    /**
     * @see Cosma\Bundle\TestingBundle\ORM\DoctrineORMSchemaTool::dropSchema
     *
     * @expectedException \Exception
     */
    public function testDropSchema_Rollback()
    {
        $schemaManager = $this->getMockBuilder('\Doctrine\DBAL\Schema\AbstractSchemaManager')
                              ->disableOriginalConstructor()
                              ->setMethods(['listTableNames', 'createSchemaConfig'])
                              ->getMockForAbstractClass()
        ;
        $schemaManager->expects($this->once())
                      ->method('listTableNames')
                      ->will($this->returnValue(['table_example']))
        ;

        $connection = $this->getMockBuilder('\Doctrine\DBAL\Connection')
                           ->disableOriginalConstructor()
                           ->setMethods(['getSchemaManager', 'connect', 'beginTransaction', 'query', 'exec', 'commit', 'rollback'])
                           ->getMock()
        ;
        $connection->expects($this->once())
                   ->method('getSchemaManager')
                   ->will($this->returnValue($schemaManager))
        ;
        $connection->expects($this->once())
                   ->method('exec')
                   ->with('TRUNCATE `table_example`')
                   ->will($this->throwException(new \Exception('Some Exception')))
        ;
        $connection->expects($this->once())
                   ->method('rollback')
                   ->will($this->returnValue(true))
        ;

        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
                              ->disableOriginalConstructor()
                              ->setMethods(['getConnection'])
                              ->getMock()
        ;
        $entityManager->expects($this->once())
                      ->method('getConnection')
                      ->will($this->returnValue($connection))
        ;

        /** @var DoctrineORMSchemaTool $mockedSchemaTool */
        $mockedSchemaTool = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\ORM\DoctrineORMSchemaTool')
                                 ->disableOriginalConstructor()
                                 ->setMethods(['createSchema'])
                                 ->getMock()
        ;

        $reflectionClassMocked = new \ReflectionClass($mockedSchemaTool);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $entityManagerProperty = $reflectionClass->getProperty('entityManager');
        $entityManagerProperty->setAccessible(true);
        $entityManagerProperty->setValue($mockedSchemaTool, $entityManager);

        $mockedSchemaTool->dropSchema();
    }

    /**
     * @see Cosma\Bundle\TestingBundle\ORM\DoctrineORMSchemaTool::createSchema
     */
    public function testCreateSchema()
    {
        $schemaManager = $this->getMockBuilder('\Doctrine\DBAL\Schema\AbstractSchemaManager')
                              ->disableOriginalConstructor()
                              ->setMethods(['listTableNames', 'createSchemaConfig'])
                              ->getMockForAbstractClass()
        ;
        $schemaManager->expects($this->once())
                      ->method('listTableNames')
                      ->will($this->returnValue(['table_example']))
        ;

        $connection = $this->getMockBuilder('\Doctrine\DBAL\Connection')
                           ->disableOriginalConstructor()
                           ->setMethods(['getSchemaManager'])
                           ->getMock()
        ;
        $connection->expects($this->once())
                   ->method('getSchemaManager')
                   ->will($this->returnValue($schemaManager))
        ;

        $metaDataOne = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
                            ->disableOriginalConstructor()
                            ->getMock()
        ;

        $metaDataTwo = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
                            ->disableOriginalConstructor()
                            ->getMock()
        ;

        $metaDataFactory = $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadataFactory')
                                ->disableOriginalConstructor()
                                ->setMethods(['getAllMetadata'])
                                ->getMock()
        ;
        $metaDataFactory->expects($this->once())
                        ->method('getAllMetadata')
                        ->will($this->returnValue([$metaDataOne, $metaDataTwo]))
        ;

        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
                              ->disableOriginalConstructor()
                              ->setMethods(['getConnection', 'getMetadataFactory'])
                              ->getMock()
        ;
        $entityManager->expects($this->once())
                      ->method('getConnection')
                      ->will($this->returnValue($connection))
        ;
        $entityManager->expects($this->once())
                      ->method('getMetadataFactory')
                      ->will($this->returnValue($metaDataFactory))
        ;

        $schemaTool = $this->getMockBuilder('Doctrine\ORM\Tools\SchemaTool')
                           ->disableOriginalConstructor()
                           ->setMethods(['createSchema'])
                           ->getMockForAbstractClass()
        ;
        $schemaTool->expects($this->once())
                   ->method('createSchema')
                   ->with([$metaDataOne, $metaDataTwo])
                   ->will($this->returnValue(null))
        ;

        /** @var DoctrineORMSchemaTool $mockedSchemaTool */
        $mockedSchemaTool = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\ORM\DoctrineORMSchemaTool')
                                 ->disableOriginalConstructor()
                                 ->setMethods(['dropSchema'])
                                 ->getMock()
        ;

        $reflectionClassMocked = new \ReflectionClass($mockedSchemaTool);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $entityManagerProperty = $reflectionClass->getProperty('entityManager');
        $entityManagerProperty->setAccessible(true);
        $entityManagerProperty->setValue($mockedSchemaTool, $entityManager);

        $schemaToolProperty = $reflectionClass->getProperty('doctrineSchemaTool');
        $schemaToolProperty->setAccessible(true);
        $schemaToolProperty->setValue($mockedSchemaTool, $schemaTool);

        $mockedSchemaTool->createSchema();
    }

    /**
     * @see Cosma\Bundle\TestingBundle\ORM\DoctrineORMSchemaTool::dropSchema
     */
    public function testDropSchema()
    {
        $schemaManager = $this->getMockBuilder('\Doctrine\DBAL\Schema\AbstractSchemaManager')
                              ->disableOriginalConstructor()
                              ->setMethods(['listTableNames', 'createSchemaConfig'])
                              ->getMockForAbstractClass()
        ;
        $schemaManager->expects($this->once())
                      ->method('listTableNames')
                      ->will($this->returnValue(['table_example']))
        ;

        $connection = $this->getMockBuilder('\Doctrine\DBAL\Connection')
                           ->disableOriginalConstructor()
                           ->setMethods(['getSchemaManager', 'connect', 'beginTransaction', 'query', 'exec', 'commit', 'rollback'])
                           ->getMock()
        ;
        $connection->expects($this->once())
                   ->method('getSchemaManager')
                   ->will($this->returnValue($schemaManager))
        ;
        $connection->expects($this->once())
                   ->method('exec')
                   ->with('TRUNCATE `table_example`')
                   ->will($this->returnValue(true))
        ;

        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
                              ->disableOriginalConstructor()
                              ->setMethods(['getConnection'])
                              ->getMock()
        ;
        $entityManager->expects($this->once())
                      ->method('getConnection')
                      ->will($this->returnValue($connection))
        ;

        /** @var DoctrineORMSchemaTool $mockedSchemaTool */
        $mockedSchemaTool = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\ORM\DoctrineORMSchemaTool')
                                 ->disableOriginalConstructor()
                                 ->setMethods(['createSchema'])
                                 ->getMock()
        ;

        $reflectionClassMocked = new \ReflectionClass($mockedSchemaTool);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $entityManagerProperty = $reflectionClass->getProperty('entityManager');
        $entityManagerProperty->setAccessible(true);
        $entityManagerProperty->setValue($mockedSchemaTool, $entityManager);

        $mockedSchemaTool->dropSchema();
    }
}
