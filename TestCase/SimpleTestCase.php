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

namespace Cosma\Bundle\TestingBundle\TestCase;

use Doctrine\ORM\EntityNotFoundException;

abstract class SimpleTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return void
     */
    protected function tearDown()
    {
        parent::tearDown();
        \Mockery::close();
    }

    /**
     * @param $entityNamespaceClass
     * @param $id
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     * @throws EntityNotFoundException
     */
    protected function getMockedEntityWithId($entityNamespaceClass, $id)
    {
        if (!class_exists($entityNamespaceClass)) {
            throw new EntityNotFoundException();
        }

        $entityModel = $this->getMock($entityNamespaceClass, array('getId'));
        $entityModel
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($id));

        return $entityModel;
    }

    /**
     * @param $entityNamespaceClass
     * @param $id
     *
     * @return mixed
     * @throws EntityNotFoundException
     */
    protected function getEntityWithId($entityNamespaceClass, $id)
    {
        if (!class_exists($entityNamespaceClass)) {
            throw new EntityNotFoundException();
        }

        $entityObject = new $entityNamespaceClass;

        $reflectionObject   = new \ReflectionObject($entityObject);
        $reflectionProperty = $reflectionObject->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($entityObject, $id);

        return $entityObject;
    }
}
