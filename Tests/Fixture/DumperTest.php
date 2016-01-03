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

namespace Cosma\Bundle\TestingBundle\Tests\Fixture;

use Cosma\Bundle\TestingBundle\Fixture\Dumper;
use Doctrine\Common\Collections\ArrayCollection;

class DumperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @see Cosma\Bundle\TestingBundle\Fixture\Dumper::getData
     */
    public function testGetData_NoAssociation()
    {
        $firstDummyEntity = new DummyEntity();
        $firstDummyEntity->setId(1);
        $firstDummyEntity->setName('first dummy entity');

        $secondDummyEntity = new DummyEntity();
        $secondDummyEntity->setId(2);
        $secondDummyEntity->setName('second dummy entity');

        $firstAnotherDummyEntity = new AnotherDummyEntity();
        $firstAnotherDummyEntity->setId(1);
        $firstAnotherDummyEntity->setName('first another dummy entity');

        $secondAnotherDummyEntity = new AnotherDummyEntity();
        $secondAnotherDummyEntity->setId(2);
        $secondAnotherDummyEntity->setName('second another dummy entity');

        $thirdAnotherDummyEntity = new AnotherDummyEntity();
        $thirdAnotherDummyEntity->setId(3);
        $thirdAnotherDummyEntity->setName('third another dummy entity');

        $firstDummyEntity->setAnotherEntity($firstAnotherDummyEntity);
        $firstDummyEntity->setAnotherEntity($thirdAnotherDummyEntity);
        $secondDummyEntity->setAnotherEntity($secondAnotherDummyEntity);

        $entityRepository = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectRepository')
                                 ->disableOriginalConstructor()
                                 ->setMethods(['findAll'])
                                 ->getMockForAbstractClass()
        ;
        $entityRepository->expects($this->once())
                         ->method('findAll')
                         ->will($this->returnValue([$firstDummyEntity, $secondDummyEntity]))
        ;

        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
                              ->disableOriginalConstructor()
                              ->setMethods(['getRepository'])
                              ->getMock()
        ;
        $entityManager->expects($this->once())
                      ->method('getRepository')
                      ->with('Cosma\Bundle\TestingBundle\Tests\Fixture\DummyEntity')
                      ->will($this->returnValue($entityRepository))
        ;

        $classMetaDataInfo = $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadataInfo')
                                  ->disableOriginalConstructor()
                                  ->setMethods(['getName', 'getIdentifier', 'getFieldNames', 'getFieldValue'])
                                  ->getMock()
        ;
        $classMetaDataInfo->expects($this->exactly(3))
                          ->method('getName')
                          ->will($this->returnValue('Cosma\Bundle\TestingBundle\Tests\Fixture\DummyEntity'))
        ;
        $classMetaDataInfo->expects($this->exactly(2))
                          ->method('getIdentifier')
                          ->will($this->returnValue(['id']))
        ;
        $classMetaDataInfo->expects($this->never())
                          ->method('getAssociationMappings')
                          ->will($this->returnValue(['anotherEntities']))
        ;
        $classMetaDataInfo->expects($this->exactly(2))
                          ->method('getFieldNames')
                          ->will($this->returnValue(['id', 'name']))
        ;
        $classMetaDataInfo->expects($this->any())
                          ->method('getFieldValue')
                          ->will($this->returnValue(456))
        ;

        $dumper = new Dumper($entityManager);

        $dumper->setAssociation(false);
        $dumper->setClassMetadataInfo($classMetaDataInfo);

        $data = $dumper->getData();

        $expected = ['cosma_bundle_testingbundle_tests_fixture_dummyentity_456' => ['id' => 456, 'name' => '456']];

        $this->assertEquals($expected, $data, 'Dump process is not correct');
    }

    /**
     * @see Cosma\Bundle\TestingBundle\Fixture\Dumper::getData
     */
    public function testGetData_WithAssociation()
    {
        $firstDummyEntity = new DummyEntity();
        $firstDummyEntity->setId(1);
        $firstDummyEntity->setName('first dummy entity');

        $secondDummyEntity = new DummyEntity();
        $secondDummyEntity->setId(2);
        $secondDummyEntity->setName('second dummy entity');

        $firstAnotherDummyEntity = new AnotherDummyEntity();
        $firstAnotherDummyEntity->setId(1);
        $firstAnotherDummyEntity->setName('first another dummy entity');

        $secondAnotherDummyEntity = new AnotherDummyEntity();
        $secondAnotherDummyEntity->setId(2);
        $secondAnotherDummyEntity->setName('second another dummy entity');

        $thirdAnotherDummyEntity = new AnotherDummyEntity();
        $thirdAnotherDummyEntity->setId(3);
        $thirdAnotherDummyEntity->setName('third another dummy entity');

        $firstDummyEntity->setAnotherEntity($firstAnotherDummyEntity);
        $firstDummyEntity->setAnotherEntity($thirdAnotherDummyEntity);
        $secondDummyEntity->setAnotherEntity($secondAnotherDummyEntity);

        $entityRepository = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectRepository')
                                 ->disableOriginalConstructor()
                                 ->setMethods(['findAll'])
                                 ->getMockForAbstractClass()
        ;
        $entityRepository->expects($this->once())
                         ->method('findAll')
                         ->will($this->returnValue([$firstDummyEntity, $secondDummyEntity]))
        ;

        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
                              ->disableOriginalConstructor()
                              ->setMethods(['getRepository'])
                              ->getMock()
        ;
        $entityManager->expects($this->once())
                      ->method('getRepository')
                      ->with('Cosma\Bundle\TestingBundle\Tests\Fixture\DummyEntity')
                      ->will($this->returnValue($entityRepository))
        ;

        $classMetaDataInfo = $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadataInfo')
                                  ->disableOriginalConstructor()
                                  ->setMethods(['getName', 'getIdentifier', 'getFieldNames', 'getFieldValue'])
                                  ->getMock()
        ;
        $classMetaDataInfo->expects($this->exactly(3))
                          ->method('getName')
                          ->will($this->returnValue('Cosma\Bundle\TestingBundle\Tests\Fixture\DummyEntity'))
        ;
        $classMetaDataInfo->expects($this->exactly(2))
                          ->method('getIdentifier')
                          ->will($this->returnValue(['id']))
        ;
        $classMetaDataInfo->expects($this->exactly(2))
                          ->method('getFieldNames')
                          ->will($this->returnValue(['id', 'name']))
        ;
//        $classMetaDataInfo->expects($this->once())
//            ->method('getAssociationMappings')
//            ->will($this->returnValue(array('anotherEntities')));
        $classMetaDataInfo->expects($this->any())
                          ->method('getFieldValue')
                          ->will($this->returnValue(123))
        ;

        $dumper = new Dumper($entityManager);

        $dumper->setAssociation(true);
        $dumper->setClassMetadataInfo($classMetaDataInfo);

        $data = $dumper->getData();

        $expected = ['cosma_bundle_testingbundle_tests_fixture_dummyentity_123' => ['id' => 123, 'name' => 123]];

        $this->assertEquals($expected, $data, 'Dump process is not correct');
    }

    /**
     * @see Cosma\Bundle\TestingBundle\Fixture\Dumper::dumpToYaml
     */
    public function testDumpToYaml()
    {
        $directoryPath = sys_get_temp_dir();

        $firstDummyEntity = new DummyEntity();
        $firstDummyEntity->setId(1);
        $firstDummyEntity->setName('first dummy entity');

        $secondDummyEntity = new DummyEntity();
        $secondDummyEntity->setId(2);
        $secondDummyEntity->setName('second dummy entity');

        $firstAnotherDummyEntity = new AnotherDummyEntity();
        $firstAnotherDummyEntity->setId(1);
        $firstAnotherDummyEntity->setName('first another dummy entity');

        $secondAnotherDummyEntity = new AnotherDummyEntity();
        $secondAnotherDummyEntity->setId(2);
        $secondAnotherDummyEntity->setName('second another dummy entity');

        $thirdAnotherDummyEntity = new AnotherDummyEntity();
        $thirdAnotherDummyEntity->setId(3);
        $thirdAnotherDummyEntity->setName('third another dummy entity');

        $firstDummyEntity->setAnotherEntity($firstAnotherDummyEntity);
        $firstDummyEntity->setAnotherEntity($thirdAnotherDummyEntity);
        $secondDummyEntity->setAnotherEntity($secondAnotherDummyEntity);

        $entityRepository = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectRepository')
                                 ->disableOriginalConstructor()
                                 ->setMethods(['findAll'])
                                 ->getMockForAbstractClass()
        ;
        $entityRepository->expects($this->once())
                         ->method('findAll')
                         ->will($this->returnValue([$firstDummyEntity, $secondDummyEntity]))
        ;

        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
                              ->disableOriginalConstructor()
                              ->setMethods(['getRepository'])
                              ->getMock()
        ;
        $entityManager->expects($this->once())
                      ->method('getRepository')
                      ->with('Cosma\Bundle\TestingBundle\Tests\Fixture\DummyEntity')
                      ->will($this->returnValue($entityRepository))
        ;

        $classMetaDataInfo = $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadataInfo')
                                  ->disableOriginalConstructor()
                                  ->setMethods(['getName', 'getIdentifier', 'getFieldNames', 'getFieldValue'])
                                  ->getMock()
        ;
        $classMetaDataInfo->expects($this->exactly(4))
                          ->method('getName')
                          ->will($this->returnValue('Cosma\Bundle\TestingBundle\Tests\Fixture\DummyEntity'))
        ;
        $classMetaDataInfo->expects($this->exactly(2))
                          ->method('getIdentifier')
                          ->will($this->returnValue(['id']))
        ;
        $classMetaDataInfo->expects($this->never())
                          ->method('getAssociationMappings')
                          ->will($this->returnValue(['anotherEntities']))
        ;
        $classMetaDataInfo->expects($this->exactly(2))
                          ->method('getFieldNames')
                          ->will($this->returnValue(['id', 'name']))
        ;
        $classMetaDataInfo->expects($this->any())
                          ->method('getFieldValue')
                          ->will($this->returnValue(456))
        ;

        $dumper = new Dumper($entityManager);

        $dumper->setAssociation(false);
        $dumper->setClassMetadataInfo($classMetaDataInfo);

        $file = $dumper->dumpToYaml($directoryPath);

        $this->assertEquals("{$directoryPath}/.yml", $file, 'Dump process is not correct');

        $expected = <<<EOT
Cosma\Bundle\TestingBundle\Tests\Fixture\DummyEntity:
    cosma_bundle_testingbundle_tests_fixture_dummyentity_456:
        id: 456
        name: 456

EOT;

        $this->assertEquals($expected, file_get_contents($file), 'Dump process is not correct');
    }

}

class DummyEntity
{
    /**
     * @type int
     */
    private $id;

    /**
     * @type string
     */
    private $name;

    /**
     * @type AnotherDummyEntity[]
     */
    private $anotherEntities;

    /**
     *
     */
    public function __construct()
    {
        $this->anotherEntities = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getAnotherEntities()
    {
        return $this->anotherEntities;
    }

    /**
     * @param AnotherDummyEntity $anotherEntity
     */
    public function setAnotherEntity(AnotherDummyEntity $anotherEntity)
    {
        $this->anotherEntities->add($anotherEntity);
    }

}

class AnotherDummyEntity
{
    /**
     * @type int
     */
    private $id;

    /**
     * @type string
     */
    private $name;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

}