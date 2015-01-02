<?php
/**
 * This file is part of the "cosma/testing-bundle" project
 *
 * (c) Cosmin Voicu<cosmin.voicu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 11/07/14
 * Time: 23:33
 */

namespace Cosma\Bundle\TestingBundle\TestCase;

abstract class SeleniumTestCase extends WebTestCase
{
    /**
     * @var \RemoteWebDriver
     */
    private $webDriver;

    protected function setUp()
    {
        parent::setUp();
        $this->webDriver = \RemoteWebDriver::create(
            static::$kernel->getContainer()->getParameter('cosma_testing.selenium.server'),
            \DesiredCapabilities::firefox()
        );
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->webDriver->close();
    }

    /**
     * @return \RemoteWebDriver
     */
    public function getWebDriver()
    {
        return $this->webDriver;
    }

    /**
     * with / at the beginning
     *
     * @param $url
     */
    public function open($url)
    {
        $this->webDriver->get($this->getDomain() . $url);
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return static::$kernel->getContainer()->getParameter('cosma_testing.selenium.domain');
    }
}
