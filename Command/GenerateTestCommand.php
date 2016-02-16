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

use Symfony\Component\Console\Helper\QuestionHelper;
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

    const DEFAULT_TESTCASE = '\PHPUnit_Framework_TestCase';

    private static $testCases = [
        'SimpleTestCase',
        'WebTestCase',
        'DBTestCase',
        'ElasticTestCase',
        'SolrTestCase',
        'SeleniumTestCase',
        'CommandTestCase',
        'RedisTestCase',
        self::DEFAULT_TESTCASE
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
     *
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
                try {

                    $testFilePath = $this->createTestFile($class);

                    $this->output->writeln("");
                    $this->output->writeln("<info>Test file for class $class was generated in $testFilePath </info>");
                    $this->output->writeln("");
                } catch (\Exception $exception) {
                    $output->writeln("<error>Generation for {$class} is aborted: {$exception->getMessage()}</error>");
                }
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
     * @param $class
     *
     * @return string
     *
     * @throws \Exception
     */
    private function createTestFile($class)
    {
        $reflectionClass = new \ReflectionClass($class);

        $testFileContent = $this->generateTestContent($reflectionClass);

        $testFileName = $this->getTestFileName($reflectionClass);

        if (file_exists($testFileName)) {
            $this->output->writeln("<error>Test file $testFileName already exists</error>");
            $question = new ConfirmationQuestion("<question>Are you sure you want to rewrite the file $testFileName ? </question> y/n (n): ",
                                                 false);
            if (!$this->questionHelper->ask($this->input, $this->output, $question)) {
                throw new \Exception("The generation for the file $testFileName was aborted");
            }
        }

        $this->checkTestDirectory($testFileName);

        $fileHandle = fopen($testFileName, 'w');

        fwrite($fileHandle, $testFileContent);

        fclose($fileHandle);

        return $testFileName;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return string
     *
     * @throws \Exception
     */
    private function generateTestContent(\ReflectionClass $reflectionClass)
    {
        $methods = $this->getMethods($reflectionClass);

        if (count($methods) == 0) {
            throw new \Exception('Class has no methods to test');
        }

        $choiceQuestion = new ChoiceQuestion(
            '<question>Pick up the TestCase for this Test class: </question>',
            self::$testCases
        );
        $testCase       = $this->questionHelper->ask($this->input, $this->output, $choiceQuestion);

        $content = "<?php" . PHP_EOL;

        $projectName = basename(realpath($this->getContainer()->get('kernel')->getRootDir() . '/../'));

        $classNamespace = $reflectionClass->getNamespaceName();

        $classShortName = $reflectionClass->getShortName();

        $className = $reflectionClass->getName();

        $commandName = $this->getName();

        $dateTime = new \DateTime();
        $date     = $dateTime->format("d/m/Y");
        $time     = $dateTime->format("H:i");

        $comment = <<<EOD
/**
 * This file is part of the "$projectName" project
 *
 * Test class for @covers $className
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

        $testNamespace = $this->getTestNamespace($classNamespace);

        $testCaseNamespace = '';

        if ($testCase != self::DEFAULT_TESTCASE) {
            $testCaseNamespace = 'use ' . self::TESTCASES_NAMESPACE . '\\' . $testCase . ';';
        }

        $testClass = <<<EOD
namespace $testNamespace;

$testCaseNamespace

class {$classShortName}Test extends {$testCase}
{
EOD;
        $content .= $testClass . PHP_EOL;

        /** @type \ReflectionMethod $method */
        foreach ($methods as $method) {
            $methodName          = $method->getName();
            $methodNameUpperCase = ucfirst($methodName);

            $methodStub = <<<EOD
    /**
     * @covers  \\{$className}::$methodName
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
     * @param \ReflectionClass $reflectionClass
     *
     * @return array
     */
    private function getMethods(\ReflectionClass $reflectionClass)
    {
        $methods = [];

        $className = $reflectionClass->getName();

        $removerMagicMethods = function (\ReflectionMethod $method) {
            $position = strpos($method->getName(), '__');

            if ($position === 0) {
                return false;
            }

            return true;
        };

        $removeParentClassMethods = function (\ReflectionMethod $method) use ($className) {

            if ($method->getDeclaringClass()->getName() == $className) {
                return true;
            }

            return false;
        };

        $publicMethods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);

        $publicMethods = array_filter($publicMethods, $removeParentClassMethods);
        $publicMethods = array_filter($publicMethods, $removerMagicMethods);

        if (!is_array($publicMethods) || count($publicMethods) == 0) {
            $this->output->writeln("<comment>No public methods to test!</comment>");
        } else {
            $this->output->writeln("<info>Public methods to test:</info>");
            array_walk(
                $publicMethods,
                function (\ReflectionMethod $method) {
                    $this->output->writeln("   * {$method->getName()}");
                }
            );
        }

        $methods = array_merge($methods, $publicMethods);

        $question = new ConfirmationQuestion("<question>Do you want to test protected functions?</question> y/n (n): ",
                                             false);
        if ($this->questionHelper->ask($this->input, $this->output, $question)) {

            $protectedMethods = $reflectionClass->getMethods(\ReflectionMethod::IS_PROTECTED);

            $protectedMethods = array_filter($protectedMethods, $removeParentClassMethods);
            $protectedMethods = array_filter($protectedMethods, $removerMagicMethods);

            if (!is_array($protectedMethods) || count($protectedMethods) == 0) {
                $this->output->writeln("<comment>No protected methods to test!</comment>");
            } else {
                $this->output->writeln("<info>Protected methods to test:</info>");
                array_walk(
                    $protectedMethods,
                    function (\ReflectionMethod $method) {
                        $this->output->writeln("   * {$method->getName()}");
                    }
                );
            }

            $methods = array_merge($methods, $protectedMethods);
        }

        return $methods;
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