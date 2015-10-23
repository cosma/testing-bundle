<?php
/**
 * This file is part of the TestingBundle project
 *
 * (c) Cosmin Voicu<cosmin.voicu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 18/10/15
 * Time: 18:24
 */

namespace Cosma\Bundle\TestingBundle\TestCase\Traits;

use Symfony\Bundle\FrameworkBundle\Client;

trait WebTestTrait
{
    /**
     * @return void
     */
    public static function setUpBeforeClass()
    {
        echo "\nWebTestTrait::setUpBeforeClass\n";

        static::bootKernel();
    }

    /**
     * Clean up Kernel usage in this test.
     */
    public static function tearDownAfterClass()
    {
        echo "\nWebTestTrait::tearDownAfterClass\n";

        static::ensureKernelShutdown();
    }

    /**
     * @return void
     */
    protected function setUp()
    {
        echo "\nWebTestTrait::setUp\n";

        static::bootKernel();
    }

    /**
     * @return void
     */
    protected function tearDown()
    {
        echo "\nWebTestTrait::tearDown\n";

        parent::tearDown();
        \Mockery::close();
    }

    /**
     * @param array $server
     *
     * @return Client
     */
    protected function getClient(array $server = array())
    {
        /** @var Client $client */
        $client = static::$kernel->getContainer()->get('test.client');

        $client->setServerParameters($server);

        return $client;
    }
}