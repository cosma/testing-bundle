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

use Predis\Client;

abstract class RedisTestCase extends WebTestCase
{
    /**
     * @var Client
     */
    private static $redisClient;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->resetRedisDatabase();
    }

    /**
     * @return Client
     */
    protected function getRedisClient()
    {
        if (null === self::$redisClient) {
            $config            = [
                'scheme'  => static::$kernel->getContainer()->getParameter('cosma_testing.redis.scheme'),
                'host'    => static::$kernel->getContainer()->getParameter('cosma_testing.redis.host'),
                'port'    => static::$kernel->getContainer()->getParameter('cosma_testing.redis.port'),
                'timeout' => static::$kernel->getContainer()->getParameter('cosma_testing.redis.timeout')
            ];
            self::$redisClient = new Client($config);
        }
        return self::$redisClient;
    }

    /**
     * @return mixed
     */
    protected function resetRedisDatabase()
    {
        $this->getRedisClient()->select(
            static::$kernel->getContainer()->getParameter('cosma_testing.redis.database')
        );
        return $this->getRedisClient()->flushdb();
    }
}
