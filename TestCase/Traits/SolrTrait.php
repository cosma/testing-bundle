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

use Solarium\Core\Client\Client as SolariumClient;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait SolrTrait
{
    /**
     * @var SolariumClient
     */
    private $solariumClient;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->resetSolrCore();
    }

    /**
     * @return \Solarium\QueryType\Update\Result
     */
    private function resetSolrCore()
    {
        $update = $this->getSolariumClient()->createUpdate();

        $update->addDeleteQuery('*:*');
        $update->addCommit();

        return $this->getSolariumClient()->update($update);
    }

    /**
     * @return SolariumClient
     */
    protected function getSolariumClient()
    {
        if (null === $this->solariumClient) {
            /** @type ContainerInterface $container */
            $container = $this->getKernel()->getContainer();

            $config               = [
                'endpoint' => [
                    'localhostTesting' => [
                        'host'    => $container->getParameter('cosma_testing.solarium.host'),
                        'port'    => $container->getParameter('cosma_testing.solarium.port'),
                        'path'    => $container->getParameter('cosma_testing.solarium.path'),
                        'core'    => $container->getParameter('cosma_testing.solarium.core'),
                        'timeout' => $container->getParameter('cosma_testing.solarium.timeout')
                    ]

                ]
            ];
            $this->solariumClient = new SolariumClient($config);
        }

        return $this->solariumClient;
    }
}