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
     * @covers SolrTestCase
     */
    public function testStaticAttributes()
    {
        $this->assertClassHasStaticAttribute('solariumClient', 'Cosma\Bundle\TestingBundle\TestCase\SolrTestCase');
    }

    /**
     * @covers SolrTestCase::setUp
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
            ->will($this->returnValue(NULL));
        $updateQuery->expects($this->once())
            ->method('addCommit')
            ->will($this->returnValue(NULL));

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
        $reflectionClass = $reflectionClassMocked->getParentClass();

        $classProperty = $reflectionClass->getProperty('class');
        $classProperty->setAccessible(TRUE);
        $classProperty->setValue($solrTestCase, 'Cosma\Bundle\TestingBundle\Tests\AppKernel');

        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(TRUE);
        $kernelProperty->setValue($solrTestCase, $kernel);

        $resetSolrCoreMethod = $reflectionClass->getMethod('resetSolrCore');
        $resetSolrCoreMethod->setAccessible(TRUE);

        $setUpMethod = $reflectionClass->getMethod('setUp');
        $setUpMethod->setAccessible(TRUE);
        $setUpMethod->invoke($solrTestCase);

        $reflectionMethod = $reflectionClass->getMethod('tearDownAfterClass');
        $reflectionMethod->invoke($solrTestCase);
    }

    /**
     * @covers SolrTestCase::getSolariumClient
     */
    public function testGetSolariumClient()
    {
        $valueMap = array(
            array('cosma_testing.solarium.host', '127.0.0.1'),
            array('cosma_testing.solarium.port', '8080'),
            array('cosma_testing.solarium.path', '/solr'),
            array('cosma_testing.solarium.core', 'test'),
            array('cosma_testing.solarium.timeout', '5')
        );

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getParameter'))
            ->getMockForAbstractClass();
        $container->expects($this->exactly(5))
            ->method('getParameter')
            ->will($this->returnValueMap($valueMap));

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getContainer'))
            ->getMockForAbstractClass();
        $kernel->expects($this->exactly(5))
            ->method('getContainer')
            ->will($this->returnValue($container));

        $solrTestCase = $this->getMockBuilder('Cosma\Bundle\TestingBundle\TestCase\SolrTestCase')
            ->disableAutoload()
            ->getMockForAbstractClass();

        $reflectionClassMocked = new \ReflectionClass($solrTestCase);
        $reflectionClass = $reflectionClassMocked->getParentClass();

        $clientProperty = $reflectionClass->getProperty('kernel');
        $clientProperty->setAccessible(TRUE);
        $clientProperty->setValue($solrTestCase, $kernel);

        $reflectionMethod = $reflectionClass->getMethod('getSolariumClient');
        $reflectionMethod->setAccessible(TRUE);

        /** @var Client $client */
        $client = $reflectionMethod->invoke($solrTestCase);

        $this->assertInstanceOf(
            'Solarium\Core\Client\Client',
            $client,
            'must return a Client object'
        );

        $reflectionMethod = $reflectionClass->getMethod('tearDownAfterClass');
        $reflectionMethod->invoke($solrTestCase);
    }
}

class SolrTestCaseExample extends SolrTestCase
{
}




