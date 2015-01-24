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

namespace Cosma\Bundle\TestingBundle\Tests\TestCase;

use Cosma\Bundle\TestingBundle\TestCase\CommandTestCase;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Bundle\FrameworkBundle\Client;

use Cosma\Bundle\TestingBundle\TestCase\WebTestCase;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class CommandTestCaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see Cosma\Bundle\TestingBundle\TestCase\CommandTestCase
     */
    public function testStaticAttributes()
    {
        $this->assertClassHasAttribute('application', 'Cosma\Bundle\TestingBundle\TestCase\CommandTestCase');
    }

    /**
     * @see CommandTestCase::executeCommand
     */
    public function testSetUpBeforeClass()
    {
        $webTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\CommandTestCase')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();


    }


}

class CommandTestCaseExample extends CommandTestCase
{
}



