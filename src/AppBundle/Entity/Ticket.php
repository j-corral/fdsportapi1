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
 * @ORM\Table(name="ticket")
 */
class Ticket
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $ticket_id;

    /**
     * @ORM\OneToOne(targetEntity="Axe")
     * @ORM\JoinColumn(name="axe_id", referencedColumnName="axe_id")
     */
    private $axe;

    /**
     * @ORM\Column(type="string")
     */
    private $home;

    /**
     * @ORM\Column(type="string")
     */
    private $visitor;

    /**
     * @ORM\Column(type="string")
     */
    private $address;

    /**
     * @ORM\ManyToOne(targetEntity="Sport")
     * @ORM\JoinColumn(name="sport_id", referencedColumnName="sport_id")
     */
    private $sport;

    /**
     * @ORM\Column(type="text")
     */
    private $category;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="media_id")
     */
    private $featured;

    /**
     * @ORM\Column(type="string")
     */
    private $intro;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

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
    public function getHome()
    {
        return $this->home;
    }

    /**
     * @param mixed $home
     */
    public function setHome($home)
    {
        $this->home = $home;
    }

    /**
     * @return mixed
     */
    public function getVisitor()
    {
        return $this->visitor;
    }

    /**
     * @param mixed $visitor
     */
    public function setVisitor($visitor)
    {
        $this->visitor = $visitor;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
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

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getFeatured()
    {
        return $this->featured;
    }

    /**
     * @param mixed $featured
     */
    public function setFeatured($featured)
    {
        $this->featured = $featured;
    }

    /**
     * @return mixed
     */
    public function getIntro()
    {
        return $this->intro;
    }

    /**
     * @param mixed $intro
     */
    public function setIntro($intro)
    {
        $this->intro = $intro;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getTicketId()
    {
        return $this->ticket_id;
    }


}