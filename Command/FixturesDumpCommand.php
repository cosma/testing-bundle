<?php
/**
 * This file is part of the "cosma/testing-bundle" project
 *
 * (c) Cosmin Voicu<cosmin.voicu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 01/025/15
 * Time: 23:33
 */

namespace Cosma\Bundle\TestingBundle\Command;

use Cosma\Bundle\TestingBundle\Fixture\Dumper;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FixturesDumpCommand extends ContainerAwareCommand
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var Dumper
     */
    private $dumper;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * Configure name, description and arguments of this command.
     */

    /**
     * app/console cosma_testing:fixtures:dump "path/to/yaml/file.yml" BundleName:Entity  [--no-relations}
     *
     * Argument :: target
     * Argument :: query DQL  - optional
     * Option :: --no-relations - optional for relations
     *
     *
     */
    protected function configure()
    {
        $this
            ->setName('cosma_testing:fixtures:dump')
            ->setDescription('Export data Table to fixtures command')
            ->addArgument(
                'dumpDirectory',
                InputArgument::REQUIRED,
                'The path to the folder where to save the fixtures'
            )
            ->addArgument(
                'entity',
                InputArgument::OPTIONAL,
                'Run command just for this specific entity',
                '*'
            )
            ->addOption(
                'no-relations',
                NULL,
                InputOption::VALUE_NONE,
                'If set, the the relations between entities will be populated'
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();

        $this->dumper = $this->getContainer()->get('cosma_testing.fixture_dumper');

        $dumpDirectory = $input->getArgument('dumpDirectory');
        $entity = $input->getArgument('entity');

        $noRelations = FALSE;

        if ($input->getOption('no-relations')) {
            $noRelations = TRUE;
        }

        if (!is_writable($dumpDirectory)) {
            throw new \Exception("Dump directory {$dumpDirectory} is not writable");
        }

        $this->dumper->setDumpDirectory($dumpDirectory);

        $this->output->writeln(PHP_EOL);

        if ('*' == $entity) {
            $this->output->writeln("[" . date('c') . "] export fixtures in {$dumpDirectory} for all entities");
            $output->writeln(PHP_EOL);

            $classMetadataCollection = $this->entityManager->getMetadataFactory()->getAllMetadata();

            /** @type ClassMetadata $classMetadata */
            foreach ($classMetadataCollection as $classMetadata) {
                $this->dumpEntityFile($classMetadata->getName(), $dumpDirectory, $noRelations);
            }
        } else {
            $this->dumpEntityFile($entity, $dumpDirectory, $noRelations);
        }

        $output->writeln("[" . date('c') . "] finished");
        $output->writeln(PHP_EOL);
    }

    /**
     * @param string     $entity
     * @param string     $dumpDirectory
     *
     * @param bool $noRelations
     */
    private function dumpEntityFile($entity, $dumpDirectory, $noRelations = FALSE)
    {
        $file = $this->dumper->dumpEntityToFile($entity, $noRelations);
        $this->output->writeln("[" . date('c') . "] dump fixture for entity {$entity} in {$file}");
        $this->output->writeln(PHP_EOL);
    }
}
