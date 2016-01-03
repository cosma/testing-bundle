<?php
/**
 * This file is part of the "cosma/testing-bundle" project
 *
 * (c) Cosmin Voicu<cosmin.voicu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 21/12/15
 * Time: 02:33
 */

namespace Cosma\Bundle\TestingBundle\Command;

use h4cc\AliceFixturesBundle\Command\LoadFilesCommand;

class FixturesImportCommand extends LoadFilesCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('cosma_testing:fixtures:import')
            ->setAliases(['h4cc_alice_fixtures:load:files'])
        ;
    }
}
