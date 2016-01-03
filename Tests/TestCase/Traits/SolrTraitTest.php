<?php
/**
 * This file is part of the TestingBundle project.
 *
 * @project    TestingBundle
 * @author     Cosmin Voicu <cosmin.voicu@oconotech.com>
 * @copyright  2015 - ocono Tech GmbH
 * @license    http://www.ocono-tech.com proprietary
 * @link       http://www.ocono-tech.com
 * @date       29/12/15
 */

namespace TestCase\Traits;

use Solarium\QueryType\Update\Query\Query;
use Solarium\QueryType\Update\Result;
use Solarium\Core\Client\Client;
use Symfony\Component\DependencyInjection\Container;

class SolrTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see \Cosma\Bundle\TestingBundle\TestCase\Traits\SolrTrait::getSolariumClient
     */
    public function testGetSolariumClient()
    {
        $container = new Container();

        $container->setParameter('cosma_testing.solarium.host', 'solr_host');
        $container->setParameter('cosma_testing.solarium.port', 8080);
        $container->setParameter('cosma_testing.solarium.path', '/solr');
        $container->setParameter('cosma_testing.solarium.core', 'test');
        $container->setParameter('cosma_testing.solarium.timeout', 500);

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\HttpKernelInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getContainer'])
                       ->getMockForAbstractClass()
        ;
        $kernel->expects($this->once())
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;

        $solrTrait = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\TestCase\Traits\SolrTrait')
                          ->disableOriginalConstructor()
                          ->setMethods(['getKernel'])
                          ->getMockForTrait()
        ;
        $solrTrait->expects($this->once())
                  ->method('getKernel')
                  ->will($this->returnValue($kernel))
        ;

        $reflectionClass = new \ReflectionClass($solrTrait);

        $reflectionMethod = $reflectionClass->getMethod('getSolariumClient');
        $reflectionMethod->setAccessible(true);
        /** @type Client $solariumClient */
        $solariumClient = $reflectionMethod->invoke($solrTrait);

        $this->assertInstanceOf('\Solarium\Core\Client\Client', $solariumClient);

        $this->assertEquals(
            [
                'scheme'  => 'http',
                'host'    => 'solr_host',
                'port'    => 8080,
                'path'    => '/solr',
                'core'    => 'test',
                'timeout' => 500,
                'key'     => 'localhostTesting',
            ],
            $solariumClient->getEndpoint()->getOptions()
        );
    }

    /**
     * @see \Cosma\Bundle\TestingBundle\TestCase\Traits\SolrTrait::resetSolrCore
     */
    public function testResetSolrCore()
    {
        $query  = $this->prophesize('\Solarium\QueryType\Update\Query\Query');
        $query->addDeleteQuery("*:*")->shouldBecalled();
        $query->addCommit()->shouldBecalled();

        $result = $this->prophesize('\Solarium\QueryType\Update\Result');

        $solariumClient = $this->getMockBuilder('\Solarium\Core\Client\Client')
                               ->disableOriginalConstructor()
                               ->setMethods(['update', 'createQuery'])
                               ->getMockForAbstractClass()
        ;
        $solariumClient->expects($this->once())
                       ->method('createQuery')
                       ->will($this->returnValue($query->reveal()))
        ;
        $solariumClient->expects($this->once())
                       ->method('update')
                       ->with($query->reveal())
                       ->will($this->returnValue($result->reveal()))
        ;


        $solrTrait = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\TestCase\Traits\SolrTrait')
                          ->disableOriginalConstructor()
                          ->getMockForTrait()
        ;

        $reflectionClass = new \ReflectionClass($solrTrait);

        $solariumClientProperty = $reflectionClass->getParentClass()->getProperty('solariumClient');
        $solariumClientProperty->setAccessible(true);
        $solariumClientProperty->setValue($solrTrait, $solariumClient);

        $reflectionMethod = $reflectionClass->getMethod('resetSolrCore');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($solrTrait);
    }
}