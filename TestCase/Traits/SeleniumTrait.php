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
    private $remoteWebDriver;

    protected function setUp()
    {
        parent::setUp();

        $this->getRemoteWebDriver();
    }

    protected function tearDown()
    {
        parent::tearDown();

        if (!is_null($this->remoteWebDriver)) {
            $this->remoteWebDriver->close();
            $this->remoteWebDriver = null;
        }
    }

    /**
     * @return RemoteWebDriver
     */
    protected function getRemoteWebDriver()
    {
        if (null === $this->remoteWebDriver) {
            $this->remoteWebDriver = RemoteWebDriver::create(
                $this->getKernel()->getContainer()->getParameter('cosma_testing.selenium.remote_server_url'),
                DesiredCapabilities::chrome()
            );
        }

        return $this->remoteWebDriver;
    }

    /**
     * @return string
     */
    public function getTestDomain()
    {
        return $this->getKernel()->getContainer()->getParameter('cosma_testing.selenium.test_domain');
    }

    /**
     * @param $url
     *
     * @return RemoteWebDriver
     */
    public function open($url)
    {
        return $this->getRemoteWebDriver()->get('http://' . $this->getTestDomain() . $url);
    }

    /**
     * @param $url
     *
     * @return RemoteWebDriver
     */
    public function openSecure($url)
    {
        return $this->getRemoteWebDriver()->get('https://' . $this->getTestDomain() . $url);
    }
}