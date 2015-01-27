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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\Query;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;

class YamlDumpCommand extends ContainerAwareCommand
{

    /**
     * The target path to where the export should be written
     *
     * @var string
     */
    private $targetPath;

    /**
     * app/console cosma_testing:yaml:generate sql target
     *
     * app/console cosma_testing:yaml:generate "select e from BundleName:Entity e" "path/to/yaml/file.yml"
     *
     * Argument :: query DQL
     * Argument :: target
     */
    protected function configure()
    {
        $this
            ->setName('cosma_testing:yaml:generate')
            ->setDescription('Convert Database entity to yaml file')
            ->addArgument('query', InputArgument::REQUIRED, 'The query to run')
            ->addArgument('target', InputArgument::OPTIONAL, 'Write the data out to a specified file');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $query            = $input->getArgument('query');
        $this->targetPath = $input->getArgument('target');

        if (!preg_match('/\bfrom\b\s*([\w:\\\]+)/i', $query, $matches)) {
            $output->writeln('<error>ERROR: Statement invalid - are you sure this valid DQL / SQL?</error>');

            return false;
        }

        $entityManager         = $this->getContainer()->get('doctrine')->getManager();
        $entityName = $entityManager->getClassMetadata($matches[1])->getName();

        try {

            /** @type Query $query */
            $query = $entityManager->createQuery($query);
            $query->setMaxResults(10);
            $rows  = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            $returnString = $entityName . ':' . PHP_EOL;
            $i            = 1;
            // loop over the rows
            foreach ($rows as $row) {
                // loop over each field
                $returnString .= '  ' . $entityManager->getClassMetadata($matches[1])->getTableName() . '_' . $i . ':' . PHP_EOL;
                foreach ($row as $fieldName => $fieldValue) {
                    $literalFlag = '';
                    if (is_null($fieldValue)) {
                        $fieldValue = '~';
                    } elseif (is_object($fieldValue)) {
                        if ($fieldValue instanceof \DateTime) {
                            $fieldValue = $fieldValue->format('Y-m-d H:i:s');
                        }
                    } elseif (is_string($fieldValue) && !is_numeric($fieldValue)) {
                        // Do have any newlines or line feeds?
                        $literalFlag = (strpos($fieldValue, '\r') !== false || strpos($fieldValue, '\n') !== false) ? '| ' : '';
                        $fieldValue  = '"' . str_replace('"', '\"', $fieldValue) . '"';
                    }
                    // Output the key/value pair
                    $returnString .= '      ' . $fieldName . ': ' . $literalFlag . $fieldValue . PHP_EOL;
                }
                $i++;
            }
            if (null !== $this->targetPath) {
                $this->doDump($returnString, $output);
            } else {
                // write to stdout
                $output->writeln($returnString);
            }

            return 1;
        } catch (\Doctrine\DBAL\DBALException $e) {
            $output->writeln('<error>ERROR: ' . $e->getMessage() . '</error>');

            return 0;
        } catch (\Doctrine\ORM\Query\QueryException $e) {
            $output->writeln('<error>ERROR: ' . $e->getMessage() . '</error>');

            return 0;
        } catch (\PDOException $e) {
            $output->writeln('<error>ERROR: ' . $e->getMessage() . '</error>');

            return 0;
        }
    }

    /**
     * Write the data out to a file
     *
     * @param string          $data
     * @param OutputInterface $output
     */
    private function doDump($data,OutputInterface $output)
    {
        if (!is_dir($dir = dirname($this->targetPath))) {
            $output->writeln('<info>[dir+]</info> ' . $dir);
            if (false === @mkdir($dir, 0777, true)) {
                throw new \RuntimeException('Unable to create directory ' . $dir);
            }
        }
        $output->writeln('<info>[file+]</info> ' . $this->targetPath);
        if (false === @file_put_contents($this->targetPath, $data)) {
            throw new \RuntimeException('Unable to write file ' . $this->targetPath);
        }
        $output->writeln('<info>Output written into ' . $this->targetPath . '</info>');
    }
}