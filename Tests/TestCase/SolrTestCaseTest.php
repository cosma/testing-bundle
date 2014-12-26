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
     * @see SolrTestCase::getSolariumClient
     */
    public function testGetSolariumClient()
    {
        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getParameter'))
            ->getMockForAbstractClass();
        $container->expects($this->once())
            ->method('getParameter')
            ->with('cosma_testing.solarium')
            ->will($this->returnValue(array(
                'host' => '127.0.0.1',
                'port' => 8080,
                'path' => '/solr/',
                'core' => 'testing',
                'timeout' => 5
            )));

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getContainer'))
            ->getMockForAbstractClass();
        $kernel->expects($this->once())
            ->method('getContainer')
            ->will($this->returnValue($container));

        $solrTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\SolrTestCase')
            ->disableAutoload()
            ->getMockForAbstractClass();

        $reflectionClassMocked = new \ReflectionClass($solrTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $clientProperty = $reflectionClass->getProperty('kernel');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($solrTestCase, $kernel);

        $reflectionMethod = $reflectionClass->getMethod('getSolariumClient');
        $reflectionMethod->setAccessible(true);

        /** @var Client $actual */
        $client = $reflectionMethod->invoke($solrTestCase);

        $this->assertInstanceOf(
            'Solarium\Core\Client\Client',
            $client,
            'must return a Client object'
        );

        $reflectionMethod = $reflectionClass->getMethod('tearDownAfterClass');
        $reflectionMethod->invoke($solrTestCase);

    }

    /**
     * @see SolrTestCase::setUp
     */
    public function testSetUp()
    {
        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $updateQuery = $this->getMockBuilder('\Solarium\QueryType\Update\Query\Query')
            ->disableOriginalConstructor()
            ->setMethods(array('addDeleteQuery', 'addCommit'))
            ->getMock();
        $updateQuery->expects($this->once())
            ->method('addDeleteQuery')
            ->with('*:*')
            ->will($this->returnValue(null));
        $updateQuery->expects($this->once())
            ->method('addCommit')
            ->will($this->returnValue(null));

        $solariumClient = $this->getMockBuilder('Solarium\Core\Client\Client')
            ->disableOriginalConstructor()
            ->setMethods(array('createUpdate', 'update'))
            ->getMock();
        $solariumClient->expects($this->once())
            ->method('createUpdate')
            ->will($this->returnValue($updateQuery));
        $solariumClient->expects($this->once())
            ->method('update')
            ->will($this->returnValue($updateQuery));

        $solrTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\SolrTestCase')
            ->disableOriginalConstructor()
            ->setMethods(array('getSolariumClient'))
            ->getMockForAbstractClass();
        $solrTestCase->expects($this->once())
            ->method('getSolariumClient')
            ->will($this->returnValue($solariumClient));

        $reflectionClassMocked = new \ReflectionClass($solrTestCase);
        $reflectionClass       = $reflectionClassMocked->getParentClass();

        $classProperty = $reflectionClass->getProperty('class');
        $classProperty->setAccessible(true);
        $classProperty->setValue($solrTestCase, 'Cosma\Bundle\TestingBundle\Tests\TestCase\AppKernel');

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernelProperty->setValue($solrTestCase, $kernel);

        $resetSolrCoreMethod = $reflectionClass->getMethod('resetSolrCore');
        $resetSolrCoreMethod->setAccessible(true);

        $setUpMethod = $reflectionClass->getMethod('setUp');
        $setUpMethod->setAccessible(true);
        $setUpMethod->invoke($solrTestCase);

        $reflectionMethod = $reflectionClass->getMethod('tearDownAfterClass');
        $reflectionMethod->invoke($solrTestCase);
    }
}

class SolrTestCaseExample extends SolrTestCase
{}




