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


class DumperTest extends \PHPUnit_Framework_TestCase
{

    public function testDumpToFile()
    {
        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $dumper = new Dumper($entityManager);

        $classMetadata = $this->getMockBuilder('Doctrine\Common\Persistence\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        //$dumper->dumpEntityToFile($classMetadata);

    }

}