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

use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class GenerateTestCommand extends ContainerAwareCommand
{
    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var QuestionHelper
     */
    private $questionHelper;

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

        if (!file_exists($file)) {
            throw new \Exception('The file doesn\'t exist');
        }

        $classes = $this->getClassesAndTraitsFromFile($file);

        if (count($classes) == 0) {
            throw new \Exception('The file contains no Class or Trait');
        }

        $this->input          = $input;
        $this->output         = $output;
        $this->questionHelper = $this->getHelper('question');

        foreach ($classes as $class) {
            $question = new Question("<question>Generate Test file for class $class ? </question> y/n (y): ", true);
            if ($this->questionHelper->ask($input, $output, $question)) {

                $this->createTestFile($class);

                $this->output->writeln("");
                $this->output->writeln("");
            }
        }
    }

    /**
     * @param string $file
     *
     * @return array
     */
    private function getClassesAndTraitsFromFile($file)
    {
        $classes   = [];
        $namespace = '';

        $tokens = token_get_all(file_get_contents($file));

        $classToken     = false;
        $namespaceToken = false;

        foreach ($tokens as $token) {
            if (is_array($token)) {
                if ($token[0] == T_NAMESPACE) {
                    $namespaceToken = true;
                } else {
                    if ($namespaceToken) {
                        if (in_array($token[0], [T_STRING, T_NS_SEPARATOR])) {
                            $namespace .= $token[1];
                        }
                    }
                }

                if ($token[0] == T_CLASS || $token[0] == T_TRAIT) {
                    $classToken = true;
                } else {
                    if ($classToken && $token[0] == T_STRING) {
                        $class = $namespace . '\\' . $token[1];
                        array_push($classes, $class);
                        $classToken = false;
                    }
                }
            } else {
                $namespaceToken = false;
            }
        }

        return $classes;
    }

    /**
     * @param string $class
     */
    private function createTestFile($class)
    {
        $reflectionClass = new \ReflectionClass($class);

        $testFileName = $this->getTestFileName($reflectionClass);

        if (file_exists($testFileName)) {
            $this->output->writeln("<error>Test file $testFileName already exists</error>");
            $question = new Question("<question>Are you sure you want to rewrite the file $testFileName ? </question> y/n (n): ",
                                     false);
            if (!$this->questionHelper->ask($this->input, $this->output, $question)) {
                return $this->output->writeln("<comment>The generation for the file $testFileName was aborted </comment>");
            }
        }

        $this->checkTestDirectory($testFileName);

        $fileHandle = fopen($testFileName, 'w');

        fwrite($fileHandle, $this->generateTestContent($reflectionClass));

        fclose($fileHandle);
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return string
     */
    private function generateTestContent(\ReflectionClass $reflectionClass)
    {
        $classNamespace = $reflectionClass->getNamespaceName();

        $testNamespace = $this->getTestNamespace($classNamespace);

        return 'sada';

    }

    /**
     * @param string $classNamespace
     *
     * @return string
     */
    private function getTestNamespace($classNamespace)
    {
        return preg_replace('/(\w+)Bundle/', '${1}Bundle\Tests', $classNamespace, 1);
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return string
     */
    private function getTestFileName(\ReflectionClass $reflectionClass)
    {
        $testDirectory = preg_replace(
            '/(\w+)Bundle/',
            '${1}Bundle' . DIRECTORY_SEPARATOR . 'Tests',
            dirname($reflectionClass->getFileName()),
            1
        );

        $testFileShortName = $reflectionClass->getShortName() . 'Test.php';

        return $testDirectory . DIRECTORY_SEPARATOR . $testFileShortName;
    }

    /**
     * @param $testFileName
     *
     * @return string
     */
    private function checkTestDirectory($testFileName)
    {
        $testDirectory = dirname($testFileName);

        if (!is_dir($testDirectory)) {
            mkdir($testDirectory, '0755', 1);
        }

        return $testDirectory;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @throws \Exception
     * @return int|null|void
     */
    protected function asdas(InputInterface $input, OutputInterface $output)
    {
        $campaignId = $input->getArgument('campaignId');
        $em         = $this->getContainer()->get('doctrine')->getManager();
        $campaign   = $em->find('OconoOptimizerBundle:Campaign', $campaignId);

        if (!$campaign instanceof Campaign) {
            throw new \Exception('Campaign with id ' . $campaignId . ' doesn\'t exist in our database');
        }

        $output->writeln('<info>Creating an arm for campaign with id: ' . $campaignId . '</info>');

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        $continueParamDefinition = true;

        while ($continueParamDefinition) {
            $arm = new Arm();
            $arm->setCampaign($campaign);
            $arm->setEnable(true);

            $question = new Question('Define new arm action: ');
            $arm->setAction($questionHelper->ask($input, $output, $question));

            $question = new Question('Define arm\'s html: ');
            $arm->setHtml($questionHelper->ask($input, $output, $question));

            $question = new Question('Define arm\'s link (default null): ');
            $arm->setUrl($questionHelper->ask($input, $output, $question));

            $question = new Question('Define arm\'s click value (default 1): ', 1);
            $arm->setClickValue($questionHelper->ask($input, $output, $question));

            $question = new Question('Define arm\'s conversion value (default 1): ', 1);
            $arm->setConversionValue($questionHelper->ask($input, $output, $question));

            $arm->setBusinessRulesValues([]); // hard coding it like this for now

            $question = new Question('Define arm\'s conversionCap (default null): ', null);
            $arm->setConversionCap($questionHelper->ask($input, $output, $question));

            try {
                $em->persist($arm);
                $em->flush();

                $output->writeln('<info>Arm with ID: ' . $arm->getId() . ' successfully created </info>');
            } catch (DBALException $exception) {
                $output->writeln('<error>Error ' . $exception->getMessage() . ' while creating the arm!</error>');
            }

            $continueArmDefinitionQuestion = new ConfirmationQuestion(
                "<question>Do you want to define  another arm?</question> y/n (n): ",
                false
            );

            if (!$questionHelper->ask($input, $output, $continueArmDefinitionQuestion)) {
                $continueParamDefinition = false;
            }
        }
    }
    
}