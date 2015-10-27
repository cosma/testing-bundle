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
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as WebTestCaseBase;

abstract class WebTestCase extends WebTestCaseBase
{
    use SimpleTestTrait;
    use WebTestTrait;
}
