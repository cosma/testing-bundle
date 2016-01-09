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
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;

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

    const TESTCASES_NAMESPACE = '\Cosma\Bundle\TestingBundle\TestCase';

    private static $testCases = [
        'SimpleTestCase',
        'WebTestCase',
        'DBTestCase',
        'ElasticTestCase',
        'SolrTestCase',
        'SeleniumTestCase',
        'CommandTestCase',
    ];

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
            $question = new ConfirmationQuestion("<question>Generate Test file for class $class ? </question> y/n (y): ", true);
            if ($this->questionHelper->ask($input, $output, $question)) {

                $testFilePath = $this->createTestFile($class);

                $this->output->writeln("");
                $this->output->writeln("<info>Test file for class $class was generated in $testFilePath </info>");
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
     *
     * @return string
     */
    private function createTestFile($class)
    {
        $reflectionClass = new \ReflectionClass($class);

        $testFileName = $this->getTestFileName($reflectionClass);

        if (file_exists($testFileName)) {
            $this->output->writeln("<error>Test file $testFileName already exists</error>");
            $question = new ConfirmationQuestion("<question>Are you sure you want to rewrite the file $testFileName ? </question> y/n (n): ",
                                                 false);
            if (!$this->questionHelper->ask($this->input, $this->output, $question)) {
                return $this->output->writeln("<comment>The generation for the file $testFileName was aborted </comment>");
            }
        }

        $this->checkTestDirectory($testFileName);

        $fileHandle = fopen($testFileName, 'w');

        fwrite($fileHandle, $this->generateTestContent($reflectionClass));

        fclose($fileHandle);

        return $testFileName;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @link  https://github.com/cosma/testing-bundle
     * @return string
     */
    private function generateTestContent(\ReflectionClass $reflectionClass)
    {
        $choiceQuestion = new ChoiceQuestion(
            '<comment>Pick up the TestCase for this Test class: </comment>',
            self::$testCases
        );
        $testCase       = $this->questionHelper->ask($this->input, $this->output, $choiceQuestion);

        $content = "<?php" . PHP_EOL;

        $projectName = basename(realpath($this->getContainer()->get('kernel')->getRootDir() . '/../'));

        $classNamespace = $reflectionClass->getNamespaceName();

        $classShortName = $reflectionClass->getShortName();

        $classNamespaceFull = $classNamespace . '\\' . $classShortName;

        $commandName = $this->getName();

        $dateTime = new \DateTime();
        $date     = $dateTime->format("d/m/Y");
        $time     = $dateTime->format("H:i");

        $comment = <<<EOD
/**
 * This file is part of the "$projectName" project
 *
 * Test class for @see $classNamespaceFull
 *
 * File is generated by command "$commandName"
 *
 * @link  https://github.com/cosma/testing-bundle
 *
 * Date: $date
 * Time: $time
 *
 */
EOD;
        $content .= $comment . PHP_EOL . PHP_EOL;

        $testNamespace     = $this->getTestNamespace($classNamespace);
        //$testCase          = 'SimpleTestCase';
        $testCaseNamespace = self::TESTCASES_NAMESPACE . '\\' . $testCase;

        $testClass = <<<EOD
namespace $testNamespace;

use {$testCaseNamespace};

class {$classShortName}Test extends {$testCase}
{
EOD;
        $content .= $testClass . PHP_EOL;

        $methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            $methodName = $method->getName();

            if ($this->isMagicMethod($methodName)) {
                continue;
            }
            $methodNameUpperCase = ucfirst($methodName);

            $methodStub = <<<EOD
    /**
     * @see  \\{$classNamespaceFull}::$methodName
     */
     public function test{$methodNameUpperCase}()
     {
        //write your test here
     }

EOD;
            $content .= $methodStub . PHP_EOL . PHP_EOL;
        }

        $content .= "}" . PHP_EOL;

        return $content;
    }

    /**
     * @param string $methodName
     *
     * @return bool
     */
    private function isMagicMethod($methodName)
    {
        $position = strpos($methodName, '__');

        if ($position === 0) {
            return true;
        }

        return false;
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
            mkdir($testDirectory, '0777', 1);
        }

        return $testDirectory;
    }
}