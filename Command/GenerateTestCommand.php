<?php
/**
 * This file is part of the "cosma/testing-bundle" project
 *
 * (c) Cosmin Voicu<cosmin.voicu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 06/01/16
 * Time: 23:33
 */

namespace Cosma\Bundle\TestingBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateTestCommand extends ContainerAwareCommand
{

    /**
     *  app/console cosma_testing:generate:test classFile
     *
     *  Argument :: classFile - required
     */
    protected function configure()
    {
        $this
            ->setName('cosma_testing:generate:test')
            ->setAliases(['cosma_testing:make:test'])
            ->setDescription('Generate a Test file for a Class file')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'The path to the Class file'
            )
            ->setHelp(<<<EOT
The <info>cosma_testing:generate:test</info> command generates a Test file for a Class file:

  <info>./app/console cosma_testing:generate:test "/path/to/class/file.php"</info>

EOT
            )
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');

    }
    
}