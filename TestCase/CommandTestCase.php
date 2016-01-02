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

use Cosma\Bundle\TestingBundle\TestCase\Traits\CommandTrait;

abstract class CommandTestCase extends WebTestCase
{
    use CommandTrait;
}
