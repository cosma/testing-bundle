<?php
/**
 * This file is part of the TestingBundle project
 *
 * (c) Cosmin Voicu<cosmin.voicu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 18/10/15
 * Time: 18:32
 */

namespace Cosma\Bundle\TestingBundle\TestCase;

use Cosma\Bundle\TestingBundle\TestCase\Traits\SimpleTestTrait;
use Cosma\Bundle\TestingBundle\TestCase\Traits\WebTestTrait;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as WebTestCaseBase;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

abstract class WebTestCase extends WebTestCaseBase
{
    use SimpleTestTrait;
    use WebTestTrait;

    /**
     * @return void
     */
    public static function setUpBeforeClass()
    {
        static::bootKernel();
    }

    /**
     * Clean up Kernel usage in this test.
     */
    public static function tearDownAfterClass()
    {
        static::ensureKernelShutdown();
    }

    /**
     * @return void
     */
    protected function setUp()
    {
        static::bootKernel();
    }

    /**
     * @return void
     */
    protected function tearDown()
    {
        parent::tearDown();
        \Mockery::close();
    }

    /**
     * @param array $server
     *
     * @return Client
     */
    protected function getClient(array $server = array())
    {
        /** @var Client $client */
        $client = static::$kernel->getContainer()->get('test.client');

        $client->setServerParameters($server);

        return $client;
    }



    /**
     * @param array $debugTrace
     *
     * @return mixed
     */
    protected function getTestClassPath(array $debugTrace)
    {
        if (isset($debugTrace[0]['file'])) {
            $testPath = strpos($debugTrace[0]['file'], "Tests/", 1);
            $filePath = substr($debugTrace[0]['file'], $testPath + 6);
            $testClassPath = str_replace('.php', '', $filePath);
        } else {
            $testClassPath = '';
        }

        return $testClassPath;
    }


}
