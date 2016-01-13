<?php
/**
 *  This file is part of the TestingBundle project
 *
 * (c) Cosmin Voicu<cosmin.voicu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 11/01/16
 * Time: 18:30
 */

namespace Cosma\Bundle\TestingBundle\TestCase\Traits;

use Predis\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait RedisTrait
{
    /**
     * @var Client
     */
    private $redisClient;

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
        if (null === $this->redisClient) {
            /** @type ContainerInterface $container */
            $container = $this->getKernel()->getContainer();

            $config            = [
                'scheme'  => $container->getParameter('cosma_testing.redis.scheme'),
                'host'    => $container->getParameter('cosma_testing.redis.host'),
                'port'    => $container->getParameter('cosma_testing.redis.port'),
                'timeout' => $container->getParameter('cosma_testing.redis.timeout')
            ];
            $this->redisClient = new Client($config);
        }

        return $this->redisClient;
    }

    /**
     * @return mixed
     */
    protected function resetRedisDatabase()
    {
        $this->getRedisClient()->select(
            $this->getKernel()->getContainer()->getParameter('cosma_testing.redis.database')
        );

        return $this->getRedisClient()->flushdb();
    }
}