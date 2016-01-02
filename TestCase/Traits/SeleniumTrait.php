<?php
/**
 * This file is part of the TestingBundle project
 *
 * (c) Cosmin Voicu<cosmin.voicu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 14/12/15
 * Time: 18:30
 */

namespace Cosma\Bundle\TestingBundle\TestCase\Traits;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

trait SeleniumTrait
{
    /**
     * @var RemoteWebDriver
     */
    private $webDriver;

    protected function setUp()
    {
        parent::setUp();

        $this->getWebDriver();
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->webDriver->close();
        $this->webDriver = null;
    }

    /**
     * @return RemoteWebDriver
     */
    protected function getWebDriver()
    {
        if (null === $this->webDriver) {
            $this->webDriver = RemoteWebDriver::create(
                $this->getKernel()->getContainer()->getParameter('cosma_testing.selenium.server'),
                DesiredCapabilities::chrome()
            );
        }

        return $this->webDriver;
    }

    /**
     * @param $url
     *
     * @return RemoteWebDriver
     */
    public function open($url)
    {
        return $this->getWebDriver()->get($this->getDomain() . $url);
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->getKernel()->getContainer()->getParameter('cosma_testing.selenium.domain');
    }
}