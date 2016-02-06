<?php

/**
 * This file is part of the "cosma/testing-bundle" project
 *
 * (c) Cosmin Voicu<cosmin.voicu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 28/12/15
 * Time: 23:33
 */

namespace Cosma\Bundle\TestingBundle\Tests\DependencyInjection;

use Cosma\Bundle\TestingBundle\DependencyInjection\Compiler\DoctrineMigrationsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class DoctrineMigrationsPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see DoctrineMigrationsPass::process
     */
    public function testProcess_truncate()
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->setDefinition('h4cc_alice_fixtures.orm.schema_tool.doctrine', new Definition());
        $containerBuilder->setParameter('cosma_testing.doctrine.cleaning_strategy', 'truncate');

        $doctrinePass = new DoctrineMigrationsPass();

        $doctrinePass->process($containerBuilder);

        $this->assertEmpty(
            $containerBuilder->getDefinition('h4cc_alice_fixtures.orm.schema_tool.doctrine')->getMethodCalls()
        );

        $containerBuilder->setParameter('doctrine_migrations.table_name', 'doctrine_table');

        $doctrinePass->process($containerBuilder);

        $this->assertEquals(
            [
                [
                    'setDoctrineMigrationsTable',
                    [
                        'doctrine_table'
                    ]
                ]
            ],
            $containerBuilder->getDefinition('h4cc_alice_fixtures.orm.schema_tool.doctrine')->getMethodCalls()
        );
    }

    /**
     * @see DoctrineMigrationsPass::process
     */
    public function testProcess_drop()
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->setDefinition('h4cc_alice_fixtures.orm.schema_tool.doctrine', new Definition());
        $containerBuilder->setParameter('cosma_testing.doctrine.cleaning_strategy', 'drop');

        $doctrinePass = new DoctrineMigrationsPass();

        $doctrinePass->process($containerBuilder);

        $this->assertEmpty(
            $containerBuilder->getDefinition('h4cc_alice_fixtures.orm.schema_tool.doctrine')->getMethodCalls()
        );

        $containerBuilder->setParameter('doctrine_migrations.table_name', 'doctrine_table');

        $doctrinePass->process($containerBuilder);

        $this->assertEmpty(
            $containerBuilder->getDefinition('h4cc_alice_fixtures.orm.schema_tool.doctrine')->getMethodCalls()
        );
    }
}
