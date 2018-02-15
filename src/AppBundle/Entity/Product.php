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
 * @ORM\Table(name="product")
 */
class Product
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $product_id;

    /**
     * @ORM\ManyToOne(targetEntity="ProductType")
     * @ORM\JoinColumn(name="product_type_id", referencedColumnName="product_type_id")
     */
    private $type;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_fixed;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\OneToOne(targetEntity="Axe")
     * @ORM\JoinColumn(name="axe_id", referencedColumnName="axe_id")
     */
    private $axe;

    /**
     * @ORM\ManyToOne(targetEntity="Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="media_id")
     */
    private $featured;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $intro;


    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->product_id;
    }


    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getisFixed()
    {
        return $this->is_fixed;
    }

    /**
     * @param mixed $is_fixed
     */
    public function setIsFixed($is_fixed)
    {
        $this->is_fixed = $is_fixed;
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
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

}