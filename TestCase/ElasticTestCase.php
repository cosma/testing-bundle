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


namespace Cosma\Bundle\TestingBundle\TestCase;


use Elastica\Client;
use Elastica\Index;
use Elastica\Type;

abstract class ElasticTestCase extends WebTestCase
{
    /**
     * @var Client
     */
    private static $elasticClient;

    /**
     * @var Index
     */
    private static $elasticIndex;

    /**
     * @var Type
     */
    private static $elasticType;

    /**
     * @return void
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
    }

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->recreateIndex();
    }

    /**
     * Clean up Kernel usage in this test.
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
    }

    /**
     * void
     */
    private function recreateIndex()
    {
        if($this->getElasticIndex()->exists()){
            $this->getElasticIndex()->delete();
        }
        $this->getElasticIndex()->create();
    }

    /**
     * @return Client
     */
    protected function getElasticClient()
    {
        if(null === self::$elasticClient){
            $config = array(
                'host' => static::$kernel->getContainer()->getParameter('cosma_testing.elastica.host'),
                'port' => static::$kernel->getContainer()->getParameter('cosma_testing.elastica.port'),
                'path' => static::$kernel->getContainer()->getParameter('cosma_testing.elastica.path'),
                'timeout' => static::$kernel->getContainer()->getParameter('cosma_testing.elastica.timeout')
            );
            self::$elasticClient = new Client($config);
        }
        return self::$elasticClient;

    }

    /**
     * @return Index
     */
    protected function getElasticIndex()
    {
        if(null === self::$elasticIndex){
            $indexName = static::$kernel->getContainer()->getParameter('cosma_testing.elastica.index');
            self::$elasticIndex = $this->getElasticClient()->getIndex($indexName);
        }
        return self::$elasticIndex;
    }

    /**
     * @return Type
     */
    protected function getElasticType()
    {
        if(null === self::$elasticType){
            $typeName = static::$kernel->getContainer()->getParameter('cosma_testing.elastica.type');
            self::$elasticType = $this->getElasticIndex()->getType($typeName);
        }
        return self::$elasticType;
    }
}
