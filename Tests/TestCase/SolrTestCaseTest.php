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

use Cosma\Bundle\TestingBundle\TestCase\SolrTestCase;
use Solarium\Client;

use Symfony\Component\DependencyInjection\Container;

class SolrTestCaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see SolrTestCase
     */
    public function testStaticAttributes()
    {
        $this->assertClassHasStaticAttribute('solariumClient', 'Cosma\Bundle\TestingBundle\TestCase\SolrTestCase');
    }

    /**
     * @see SolrTestCase::testGetSolariumClient
     */
    public function atestGetSolariumClient()
    {
        $solrTestCase = $this->getMockedSolrTestCase();

        $reflectionClass = new \ReflectionClass($solrTestCase);

        $reflectionMethod = $reflectionClass->getMethod('getSolariumClient');
        $reflectionMethod->setAccessible(true);

        /** @var Client $actual */
        $client = $reflectionMethod->invoke($solrTestCase);

        $this->assertInstanceOf(
            'Solarium\Core\Client\Client',
            $client,
            'must return a Client object'
        );
    }


    /**
     * @return SolrTestCase
     */
    private function getMockedSolrTestCase()
    {

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableAutoload()
            ->setMethods(array('getParameter'))
            ->getMock();

        $client = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Client')
            ->disableAutoload()
            ->setMethods(array('getContainer'))
            ->getMock();
        $client->expects($this->any())
            ->method('getContainer')
            ->with()
            ->will($this->returnValue($container));


        $solrTestCaseMocked = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\SolrTestCase')
            ->disableAutoload()
            ->setMethods(array())
            ->getMockForAbstractClass();

        $reflectionClassMocked = new \ReflectionClass($solrTestCaseMocked);
        $reflectionClass       = $reflectionClassMocked->getParentClass();


        $clientProperty = $reflectionClass->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($solrTestCaseMocked, $client);

        return $solrTestCaseMocked;
    }




}

class SolrTestCaseExample extends SolrTestCase
{
}



