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


class AnotherExampleEntity
{
    private $id;

    private $firstName;

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
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $name
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }
}