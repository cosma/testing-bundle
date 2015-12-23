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
 * Time: 18:32
 */

namespace Cosma\Bundle\TestingBundle\TestCase;

use Cosma\Bundle\TestingBundle\TestCase\Traits\SimpleTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as WebTestCaseBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class WebTestCase extends WebTestCaseBase
{
    use SimpleTestTrait;

    protected function setUp()
    {
        parent::setUp();
        static::bootKernel();
    }

    /**
     * @return KernelInterface
     */
    protected function getKernel()
    {

        if (null === static::$kernel) {
            static::bootKernel();
        }

        return static::$kernel;
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->getKernel()->getContainer();
    }

    /**
     * @param array $server
     *
     * @return Client
     */
    protected function getClient(array $server = [])
    {
        /** @var Client $client */
        $client = $this->getContainer()->get('test.client');

        $client->setServerParameters($server);

        return $client;
    }
}
