<?php
/**
 * This file is part of the "cosma/testing-bundle" project
 *
 * (c) Cosmin Voicu<cosmin.voicu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 01/27/15
 * Time: 23:33
 */

namespace Cosma\Bundle\TestingBundle\TestCase;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;

abstract class CommandTestCase extends WebTestCase
{
    /**
     * @type Application
     */
    private $application;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $client = static::createClient();

        $this->application = new Application($client->getKernel());
        $this->application->setAutoExit(false);
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
        $input = new StringInput($command);
        $output = new StreamOutput($temporaryFile);

        $this->application->run($input, $output);

        fseek($temporaryFile, 0);
        $output = '';
        while (!feof($temporaryFile)) {
            $output = fread($temporaryFile, 4096);
        }
        fclose($temporaryFile);

        return $output;
    }
}
