<?php
/**
 *  This file is part of the TestingBundle project
 *
 * (c) Cosmin Voicu<cosmin.voicu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 11/01/16
 * Time: 18:30
 */

namespace Cosma\Bundle\TestingBundle\TestCase;

use Cosma\Bundle\TestingBundle\TestCase\Traits\RedisTrait;

abstract class RedisTestCase extends WebTestCase
{
    use RedisTrait;
}