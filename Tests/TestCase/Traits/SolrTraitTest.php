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

        $container->setParameter('cosma_testing.solarium.host', '127.0.0.1');
        $container->setParameter('cosma_testing.solarium.port', 8080);
        $container->setParameter('cosma_testing.solarium.path', '/solr');
        $container->setParameter('cosma_testing.solarium.core', 'test');
        $container->setParameter('cosma_testing.solarium.timeout', 10);

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\HttpKernelInterface')
                       ->disableOriginalConstructor()
                       ->setMethods(['getContainer'])
                       ->getMockForAbstractClass()
        ;
        $kernel->expects($this->once())
               ->method('getContainer')
               ->will($this->returnValue($container))
        ;

        $testCaseTrait = $this->getMockBuilder('\Cosma\Bundle\TestingBundle\TestCase\Traits\SolrTrait')
                          ->disableOriginalConstructor()
                          ->setMethods(['getKernel'])
                          ->getMockForTrait()
        ;
        $testCaseTrait->expects($this->once())
                  ->method('getKernel')
                  ->will($this->returnValue($kernel))
        ;

        $reflectionClass = new \ReflectionClass($testCaseTrait);

        $reflectionMethod = $reflectionClass->getMethod('getSolariumClient');
        $reflectionMethod->setAccessible(true);

        /** @type Client $solariumClient */
        $solariumClient = $reflectionMethod->invoke($testCaseTrait);

        $this->assertInstanceOf('\Solarium\Core\Client\Client', $solariumClient);

        $this->assertEquals(
            [
                'scheme'  => 'http',
                'host'    => '127.0.0.1',
                'port'    => 8080,
                'path'    => '/solr',
                'core'    => 'test',
                'timeout' => 10,
                'key'     => 'localhostTesting',
            ],
            $solariumClient->getEndpoint()->getOptions()
        );

        return [ $testCaseTrait, $reflectionClass];
    }

    /**
     * @see \Cosma\Bundle\TestingBundle\TestCase\Traits\SolrTrait::resetSolrCore
     *
     * @depends testGetSolariumClient
     */
    public function testResetSolrCore(array $options)
    {
        /** @type \ReflectionClass $reflectionClass */
        list($testCaseTrait, $reflectionClass) = $options;

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

        $property = $reflectionClass->getParentClass()->getProperty('solariumClient');
        $property->setAccessible(true);
        $property->setValue($testCaseTrait, $solariumClient);

        $method = $reflectionClass->getMethod('resetSolrCore');
        $method->setAccessible(true);
        $method->invoke($testCaseTrait);
    }
}