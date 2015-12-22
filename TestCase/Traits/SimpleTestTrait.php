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

trait SimpleTestTrait
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

        $entityModel = $this->getMock($entityNamespaceClass, ['getId']);
        $entityModel
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($id))
        ;

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

    /**
     * @return mixed|string
     */
    protected function getTestClassPath()
    {
        $debugTrace = debug_backtrace();

        if (isset($debugTrace[0]['file'])) {
            $testPath      = strpos($debugTrace[0]['file'], "src/", 1);
            $filePath      = substr($debugTrace[0]['file'], $testPath + 4);
            $testClassPath = str_replace('.php', '', $filePath);
        } else {
            $testClassPath = '';
        }

        return $testClassPath;
    }
}