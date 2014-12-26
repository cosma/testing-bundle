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

namespace Cosma\Bundle\TestingBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CosmaTestingExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        if(isset($config['fixture_path'])){
            $container->setParameter('cosma_testing.fixture_path', $config['fixture_path']);
        }

        if(isset($config['fixture_table_directory'])){
            $container->setParameter('cosma_testing.fixture_table_directory', $config['fixture_table_directory']);
        }

        if(isset($config['fixture_test_directory'])){
            $container->setParameter('cosma_testing.fixture_test_directory', $config['fixture_test_directory']);
        }

        if(isset($config['solarium']['host'])){
            $container->setParameter('cosma_testing.solarium', $config['solarium']);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}
