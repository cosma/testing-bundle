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

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
            $container = $this->getContainer();

            if ($container instanceof ContainerInterface) {
                $entityManager = $container->get('doctrine.orm.entity_manager');
                if ($entityManager instanceof EntityManager) {
                    $entityName = $entityManager
                        ->getRepository($entityName)
                        ->getClassName()
                    ;
                }
            }
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

    /*
     * {@inheritdoc}
     *
     */
    public function runBare()
    {
        for ($i = 0; $i <= $this->getNumberOfRetries(); $i++) {
            try {
                if ($i > 0) {
                    //purple on yellow background colour
                    echo "\033[35m\033[43mR\033[0m";
                }
                parent::runBare();

                return;
            } catch (\Exception $exception) {
            }
        }
        if ($exception) {
            throw $exception;
        }
    }

    /**
     * @return int
     */
    private function getNumberOfRetries()
    {
        $annotations = $this->getAnnotations();

        if (isset($annotations['method']['retry'])) {
            if (
                isset($annotations['method']['retry'][0]) &&
                is_numeric($annotations['method']['retry'][0])

            ) {
                return $annotations['method']['retry'][0];
            }

            return 1;
        }

        if (isset($annotations['class']['retry'])) {
            if (
                isset($annotations['class']['retry'][0]) &&
                is_numeric($annotations['class']['retry'][0])

            ) {
                return $annotations['class']['retry'][0];
            }

            return 1;
        }

        return 0;
    }
}