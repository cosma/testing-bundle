<?php

/**
 * This file is part of the "cosma/testing-bundle" project
 *
 * (c) Cosmin Voicu<cosmin.voicu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 26/06/15
 * Time: 11:33
 */

namespace Cosma\Bundle\TestingBundle\DependencyInjection\Compiler;

use Cosma\Bundle\TestingBundle\ORM\DoctrineORMSchemaTool;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DoctrineMigrationsPass implements CompilerPassInterface
{
    /**
     * Set the doctrine migrations table if there is one
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $cleaningStrategy = $container->getParameter('cosma_testing.doctrine.cleaning_strategy');

        if (
            DoctrineORMSchemaTool::DOCTRINE_CLEANING_TRUNCATE == $cleaningStrategy &&
            $container->hasParameter('doctrine_migrations.table_name')
        ) {
            $doctrineMigrationsTable = $container->getParameter('doctrine_migrations.table_name');
            $definition              = $container->findDefinition('h4cc_alice_fixtures.orm.schema_tool.doctrine');

            $definition->addMethodCall('setDoctrineMigrationsTable', [$doctrineMigrationsTable]);
        }
    }
}
