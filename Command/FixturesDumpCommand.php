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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
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
     * Configure name, description and arguments of this command.
     */

    /**
     *
     * app/console cosma_testing:fixtures:dump [-a|--associations] dumpDirectory [entity]
     *
     * app/console cosma_testing:fixtures:dump [-a|--associations] "path/to/dump/directory" BundleName:Entity
     *
     * Argument :: dump directory - required
     * Argument :: entity  - if not specified will save all entities
     * Option :: --associations / -a - saves the associations between entities, too
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
                NULL
            )
            ->addOption(
                'associations',
                'a',
                InputOption::VALUE_NONE,
                'If set, the the relations between entities will be populated'
            )
            ->setHelp(<<<EOT
The <info>cosma_testing:fixtures:dump</info> command dump data into file fixtures from your database:

  <info>./app/console cosma_testing:fixtures:dump "/path/to/dump/directory"</info>

By default, the fixtures for all the entities managed by Doctrine will be saved to the specified dump directory.
You can also optionally specify the exact entity as the second argument:

  <info>./app/console cosma_testing:fixtures:dump "/path/to/dump/directory" "BundleName:EntityName"</info>

If you want to include in the fixtures all the associations of the entity, you can use the <info>--associations</info> or <info>-a</info> option:

  <info>./app/console cosma_testing:fixtures:dump --associations "/path/to/dump/directory" "BundleName:EntityName"</info>

EOT
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $dumpDirectory = $input->getArgument('dumpDirectory');
        $entity = $input->getArgument('entity');

        $associations = FALSE;
        if ($input->getOption('associations')) {
            $associations = TRUE;
        }

        $this->output = $output;

        $this->dumper = $this->getContainer()->get('cosma_testing.fixture_dumper');

        /** @type EntityManagerInterface $entityManager */
        $entityManager = $this->getContainer()->get('doctrine')->getManager();


        $this->dumper->setAssociation($associations);


        $this->output->writeln(PHP_EOL);

        if ($entity) {$classMetadataInfo = $entityManager->getMetadataFactory()->getMetadataFor($entity);
            $this->dumpFile($classMetadataInfo, $dumpDirectory);

        } else {
            $this->output->writeln("[" . date('c') . "] export fixtures in {$dumpDirectory} for all entities");
            $this->output->writeln(PHP_EOL);

            $classMetadataInfoCollection = $entityManager->getMetadataFactory()->getAllMetadata();

            /** @type ClassMetadataInfo $classMetadataInfo */
            foreach ($classMetadataInfoCollection as $classMetadataInfo) {
                $this->dumpFile($classMetadataInfo, $dumpDirectory);
            }
        }

        $this->output->writeln("[" . date('c') . "] finished");
        $this->output->writeln(PHP_EOL);
    }

    /**
     * @param ClassMetadataInfo $classMetadataInfo
     * @param string $dumpDirectory
     *
     * return void
     */
    private function dumpFile(ClassMetadataInfo $classMetadataInfo, $dumpDirectory)
    {
        $this->output->writeln("[" . date('c') . "] dumping {$classMetadataInfo->getName()} ...");

        $this->dumper->setClassMetadataInfo($classMetadataInfo);
        $file = $this->dumper->dumpToYaml($dumpDirectory);

        $this->output->writeln("[" . date('c') . "] successfully dumped in file  {$file}");
        $this->output->writeln(PHP_EOL);
    }
}
