<?php
/**
 *  This file is part of the TestingBundle project
 *
 * (c) Cosmin Voicu<cosmin.voicu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 02/01/16
 * Time: 18:30
 */

namespace Cosma\Bundle\TestingBundle\TestCase\Traits;

use Elastica\Client;
use Elastica\Index;
use Elastica\Type;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait ElasticTrait
{
    /**
     * @var Client
     */
    private $elasticClient;

    /**
     * @var Index
     */
    private $elasticIndex;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->recreateIndex();
    }

    /**
     * @return Client
     */
    protected function getElasticClient()
    {
        if (null === $this->elasticClient) {

            /** @type ContainerInterface $container */
            $container = $this->getKernel()->getContainer();

            $config              = [
                'host'    => $container->getParameter('cosma_testing.elastica.host'),
                'port'    => $container->getParameter('cosma_testing.elastica.port'),
                'path'    => $container->getParameter('cosma_testing.elastica.path'),
                'timeout' => $container->getParameter('cosma_testing.elastica.timeout')
            ];
            $this->elasticClient = new Client($config);
        }

        return $this->elasticClient;
    }

    /**
     * @return Index
     */
    protected function getElasticIndex()
    {
        if (null === $this->elasticIndex) {

            $elasticClient = $this->getElasticClient();

            $indexName          = $this->getKernel()->getContainer()->getParameter('cosma_testing.elastica.index');
            $this->elasticIndex = $elasticClient->getIndex($indexName);
        }

        return $this->elasticIndex;
    }

    /**
     * void
     */
    protected function recreateIndex()
    {
        if ($this->getElasticIndex()->exists()) {
            $this->getElasticIndex()->delete();
        }
        $this->getElasticIndex()->create();
    }
}