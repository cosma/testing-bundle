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

namespace Cosma\Bundle\TestingBundle;

use Cosma\Bundle\TestingBundle\DependencyInjection\Compiler\DoctrineMigrationsPass;
use Cosma\Bundle\TestingBundle\DependencyInjection\CosmaTestingExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class TestingBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new CosmaTestingExtension();
    }

    /**
     * Adds DoctrineMigrationsPass on bundle load
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DoctrineMigrationsPass());
    }
}
