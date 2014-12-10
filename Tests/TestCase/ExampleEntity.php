<?php
/**
 * 
 *
 * PHP Version 5.4
 *
 * @category 
 * @package
 * @subpackage
 * @author     Cosmin Voicu <cosmin.voicu@magari-internet.de>
 * @copyright  2014 magari internet GmbH
 * @license
 * @version    SVN: $Id$
 * @link
 */

namespace Cosma\Bundle\TestingBundle\Tests\TestCase;


class ExampleEntity
{
    private $id;

    private $name;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}