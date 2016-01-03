<?php
/**
 *  This file is part of the TestingBundle project
 *
 * (c) Cosmin Voicu<cosmin.voicu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 02/01/16
 * Time: 18:30
 */

namespace Cosma\Bundle\TestingBundle\TestCase\Traits;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;

trait CommandTrait
{
    /**
     * @type Application
     */
    private $consoleApplication;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->getConsoleApplication();
    }

    /**
     * @return Application
     */
    protected function getConsoleApplication()
    {
        if (null === $this->consoleApplication) {
            $this->consoleApplication = new Application($this->getKernel());
            $this->consoleApplication->setAutoExit(false);
        }

        return $this->consoleApplication;
    }

    /**
     * Runs a command and returns it output
     *
     * @param string $command
     *
     * @return string|StreamOutput
     * @throws \Exception
     */
    public function executeCommand($command)
    {
        $temporaryFile = tmpfile();
        $input         = new StringInput($command);
        $output        = new StreamOutput($temporaryFile);

        $this->getConsoleApplication()->run($input, $output);

        fseek($temporaryFile, 0);
        $output = '';
        while (!feof($temporaryFile)) {
            $output = fread($temporaryFile, 4096);
        }
        fclose($temporaryFile);

        return $output;
    }
}