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
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $managerRegistryMock;
    public function setUp()
    {
        $this->managerRegistryMock = $this->getMockBuilder('\Doctrine\Common\Persistence\ManagerRegistry')
                                          ->setMethods(array('getManagers'))
                                          ->getMockForAbstractClass();
        $this->managerRegistryMock->expects($this->any())
                                  ->method('getManagers')
                                  ->will($this->returnValue(array()));
    }
    public function testConstruct()
    {
        $schemaTool = new DoctrineORMSchemaTool($this->managerRegistryMock);
        $this->assertInstanceOf('\h4cc\AliceFixturesBundle\ORM\SchemaToolInterface', $schemaTool);
    }
    public function testDropSchema()
    {
        $schemaTool = new DoctrineORMSchemaTool($this->managerRegistryMock);
        // Not testing any further here for now, because mocking for DoctrineSchemaTool needs some effort.
        $schemaTool->dropSchema();
    }
    public function testCreateSchema()
    {
        $schemaTool = new DoctrineORMSchemaTool($this->managerRegistryMock);
        // Not testing any further here for now, because mocking for DoctrineSchemaTool needs some effort.
        $schemaTool->createSchema();
    }
}