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

class Dumper
{

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $dumpDirectory;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }

    /**
     * @param ClassMetadata $classMetadata
     * @param bool                                $noRelations
     *
     * @return bool
     */
    public function dumpToFile(ClassMetadata $classMetadata, $noRelations = true)
    {

        return true;

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
}