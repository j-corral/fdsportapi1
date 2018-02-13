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
     * @ORM\Column(type="string")
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
}