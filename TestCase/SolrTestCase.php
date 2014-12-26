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

use Solarium\Core\Client\Client as SolariumClient;

abstract class SolrTestCase extends WebTestCase
{
    /**
     * @var SolariumClient
     */
    private static $solariumClient;

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
        $this->resetSolrCore();
    }

    /**
     * Clean up Kernel usage in this test.
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
    }

    /**
     * @return \Solarium\QueryType\Update\Result
     */
    private function resetSolrCore()
    {
        /**  @var SolariumClient $solariumClient */
        $solariumClient = $this->getSolariumClient();
        $update     = $solariumClient->createUpdate();

        $update->addDeleteQuery('*:*');
        $update->addCommit();
        return $solariumClient->update($update);

    }

    /**
     * @return SolariumClient
     */
    protected function getSolariumClient()
    {
        if(null === self::$solariumClient){

            $config = array(
                'endpoint' => array(
                    'localhostTesting' => static::$kernel->getContainer()->getParameter('cosma_testing.solarium')
                )
            );
            self::$solariumClient = new SolariumClient($config);
        }
        return self::$solariumClient;

    }
}
