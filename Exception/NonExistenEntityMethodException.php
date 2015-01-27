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
namespace Cosma\Bundle\TestingBundle\Exception;

class NonExistentEntityMethodException extends \Exception
{
    /**
     * @param string $entity
     * @param string $method
     */
    public function __construct($entity, $method)
    {
        $message =  "Entity {$entity} does not have method {$method}";

        parent::__construct($message);
    }
}