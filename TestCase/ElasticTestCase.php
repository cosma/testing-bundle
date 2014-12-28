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

use FOS\ElasticaBundle\Elastica\Client;

abstract class ElasticTestCase extends WebTestCase
{
    /**
     * @var Client
     */
    private static $elasticaClient;

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
        $this->resetIndex();
    }

    /**
     * Clean up Kernel usage in this test.
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
    }

    /**
     * @return \Elastica\Response
     */
    private function resetIndex()
    {
        /**  @var Client $elasticaClient */
        $elasticaClient = $this->getElasticaClient();
        $elasticaClient->request('/deleteAll');
        return $elasticaClient->getLastResponse();

    }

    /**
     * @return Client
     */
    protected function getElasticaClient()
    {
        if(null === self::$elasticaClient){
            $config = array(

            );
            self::$elasticaClient = new Client($config);
        }
        return self::$elasticaClient;

    }
}
