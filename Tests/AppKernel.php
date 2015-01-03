<?php
/**
 * This file is part of the "cosma/testing-bundle" project
 *
 * mocked AppKernel
 *
 * (c) Cosmin Voicu<cosmin.voicu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 11/07/14
 * Time: 23:33
 */

namespace Cosma\Bundle\TestingBundle\Tests;

use Symfony\Component\HttpKernel\HttpKernel;

class AppKernel extends HttpKernel
{
    public function __construct(){}
    public function boot(){}
    public function shutdown(){}
    //public function getContainer(){}
}