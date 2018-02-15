<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/02/18
 * Time: 16:10
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $user_id;

    /**
     * @ORM\OneToOne(targetEntity="Cookie")
     * @ORM\JoinColumn(name="cookie_id", referencedColumnName="cookie_id")
     */
    private $cookie;

    /**
     * @ORM\Column(type="string")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string")
     */
    private $lastname;

    /**
     * @ORM\OneToOne(targetEntity="Axe")
     * @ORM\JoinColumn(name="axe_id", referencedColumnName="axe_id")
     */
    private $axe;

    /**
     * @return mixed
     */
    public function getCookie()
    {
        return $this->cookie;
    }

    /**
     * @param mixed $cookie
     */
    public function setCookie($cookie)
    {
        $this->cookie = $cookie;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return mixed
     */
    public function getAxe()
    {
        return $this->axe;
    }

    /**
     * @param mixed $axe
     */
    public function setAxe($axe)
    {
        $this->axe = $axe;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }



}