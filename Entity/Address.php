<?php
namespace Cosma\Bundle\TestingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * DoctrineEntity
 *
 * @ORM\Entity()
 * @ORM\Table(
 *      name="addresses",
 *      uniqueConstraints={
 *      },
 *      indexes={
 *      }
 * )
 *
 */
class Address {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="street_name", type="string", length=255)
     */
    private $streetName;

    /**
     * @var string
     *
     * @ORM\Column(name="street_number", type="string", length=10)
     */
    private $streetNumber;


    /**
     * @var string
     *
     * @ORM\Column(name="post_code", type="string", length=10)
     */
    private $postCode;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="addresses")
     **/

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="addresses")
     * @ORM\JoinTable(name="users_addresses")
     */
    private $users;


    public function __construct()
    {
        $this->users = new ArrayCollection();
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getStreetName()
    {
        return $this->streetName;
    }

    /**
     * @param string $streetName
     */
    public function setStreetName($streetName)
    {
        $this->streetName = $streetName;
    }

    /**
     * @return string
     */
    public function getStreetNumber()
    {
        return $this->streetNumber;
    }

    /**
     * @param string $streetNumber
     */
    public function setStreetNumber($streetNumber)
    {
        $this->streetNumber = $streetNumber;
    }

    /**
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param User $user
     */
    public function addUser(User $user)
    {
        $user->addAddress($this);
        $this->users[] = $user;
    }

    /**
     * @param ArrayCollection $users
     */
    public function setUsers(ArrayCollection $users)
    {
        $this->users = $users;
    }


    /**
     * @return string
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

    /**
     * @param string $zipCode
     */
    public function setPostCode($zipCode)
    {
        $this->postCode = $zipCode;
    }



} 