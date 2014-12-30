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
    private static $elasticaClient;

    /**
     * @var Index
     */
    private static $elasticaIndex;

    /**
     * @var Type
     */
    private static $elasticaType;

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
        $this->getElasticaIndex()->delete();
        $this->getElasticaIndex()->create();
    }

    /**
     * @return Client
     */
    protected function getElasticaClient()
    {
        if(null === self::$elasticaClient){
            $config = array(
                'host' => static::$kernel->getContainer()->getParameter('cosma_testing.elastica.host'),
                'port' => static::$kernel->getContainer()->getParameter('cosma_testing.elastica.port'),
                'path' => static::$kernel->getContainer()->getParameter('cosma_testing.elastica.path'),
                'timeout' => static::$kernel->getContainer()->getParameter('cosma_testing.elastica.timeout')
            );
            self::$elasticaClient = new Client($config);
        }
        return self::$elasticaClient;

    }

    /**
     * @return Index
     */
    protected function getElasticaIndex()
    {
        if(null === self::$elasticaIndex){
            $indexName = static::$kernel->getContainer()->getParameter('cosma_testing.elastica.index');
            self::$elasticaIndex = $this->getElasticaClient()->getIndex($indexName);
        }
        return self::$elasticaIndex;
    }

    /**
     * @return Type
     */
    protected function getElasticaType()
    {
        if(null === self::$elasticaType){
            $typeName = static::$kernel->getContainer()->getParameter('cosma_testing.elastica.type');
            self::$elasticaType = $this->getElasticaindex()->getType($typeName);
        }
        return self::$elasticaType;
    }
}
