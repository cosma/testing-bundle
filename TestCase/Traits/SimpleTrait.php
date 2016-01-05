<?php
/**
 * This file is part of the TestingBundle project
 *
 * (c) Cosmin Voicu<cosmin.voicu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 18/10/15
 * Time: 18:23
 */

namespace Cosma\Bundle\TestingBundle\TestCase\Traits;

use Doctrine\ORM\EntityNotFoundException;

trait SimpleTrait
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
     * @param $entityName
     * @param $id
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     * @throws EntityNotFoundException
     */
    protected function getMockedEntityWithId($entityName, $id)
    {
        if (method_exists($this, 'getEntityManager')) {
            $entityName = $this->getEntityManager()
                               ->getRepository($entityName)
                               ->getClassName()
            ;
        } elseif (method_exists($this, 'getContainer')) {
            $entityName = $this->getContainer()
                               ->get('doctrine.orm.entity_manager')
                               ->getRepository($entityName)
                               ->getClassName()
            ;
        }

        if (!class_exists($entityName)) {
            throw new EntityNotFoundException();
        }

        $entityModel = $this->getMock($entityName, ['getId']);

        $entityModel->expects($this->any())
                    ->method('getId')
                    ->will($this->returnValue($id))
        ;

        return $entityModel;
    }

    /**
     * @param $entityName
     * @param $id
     *
     * @return mixed
     * @throws EntityNotFoundException
     */
    protected function getEntityWithId($entityName, $id)
    {
        if (method_exists($this, 'getEntityManager')) {
            $entityName = $this->getEntityManager()->getRepository($entityName)->getClassName();
        }

        if (!class_exists($entityName)) {
            throw new EntityNotFoundException();
        }

        $entityObject = new $entityName;

        $reflectionObject   = new \ReflectionObject($entityObject);
        $reflectionProperty = $reflectionObject->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($entityObject, $id);

        return $entityObject;
    }

    /**
     * @return mixed|string
     */
    protected function getTestClassPath()
    {
        $testClassPath = false;

        $debugTrace = debug_backtrace();

        if (isset($debugTrace[0]['file'])) {
            $testPath      = strpos($debugTrace[0]['file'], "src/", 1);
            $filePath      = substr($debugTrace[0]['file'], $testPath + 4);
            $testClassPath = str_replace('.php', '', $filePath);
        }

        return $testClassPath;
    }
}