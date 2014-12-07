<?php

namespace Cosma\Bundle\TestingBundle\TestCase;

use Doctrine\ORM\EntityNotFoundException;

class SimpleTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $entityNamespaceClass
     * @param $id
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
     * @return mixed
     * @throws EntityNotFoundException
     */
    protected function getEntityWithId($entityNamespaceClass, $id)
    {
        if (!class_exists($entityNamespaceClass)) {
            throw new EntityNotFoundException();
        }

        $entityObject    = new $entityNamespaceClass;

        $reflectionObject   = new \ReflectionObject($entityObject);
        $reflectionProperty = $reflectionObject->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($entityObject, $id);

        return $entityObject;
    }
} 