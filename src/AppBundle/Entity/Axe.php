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
 * @ORM\Table(name="axe")
 */
class Axe
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $axe_id;

    /**
     * @ORM\Column(type="float")
     */
    private $male;

    /**
     * @ORM\Column(type="float")
     */
    private $female;

    /**
     * @ORM\Column(type="string")
     */
    private $brand;

    /**
     * @ORM\Column(type="float")
     */
    private $age;

    /**
     * @ORM\Column(type="string")
     */
    private $city;

    /**
     * @ORM\Column(type="float")
     */
    private $csp;

    /**
     * @ORM\Column(type="string")
     */
    private $sport;

    /**
     * @return mixed
     */
    public function getAxeId()
    {
        return $this->axe_id;
    }

    /**
     * @param mixed $axe_id
     */
    public function setAxeId($axe_id)
    {
        $this->axe_id = $axe_id;
    }

    /**
     * @return mixed
     */
    public function getMale()
    {
        return $this->male;
    }

    /**
     * @param mixed $male
     */
    public function setMale($male)
    {
        $this->male = $male;
    }

    /**
     * @return mixed
     */
    public function getFemale()
    {
        return $this->female;
    }

    /**
     * @param mixed $female
     */
    public function setFemale($female)
    {
        $this->female = $female;
    }

    /**
     * @return mixed
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param mixed $brand
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
    }

    /**
     * @return mixed
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param mixed $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getCsp()
    {
        return $this->csp;
    }

    /**
     * @param mixed $csp
     */
    public function setCsp($csp)
    {
        $this->csp = $csp;
    }

    /**
     * @return mixed
     */
    public function getSport()
    {
        return $this->sport;
    }

    /**
     * @param mixed $sport
     */
    public function setSport($sport)
    {
        $this->sport = $sport;
    }



}