<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\Groups;

class EntityBase {

    /**
    * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue
    * 
    * @Groups({"base"})
    */
    protected $id;

    /**
    * @ORM\Column(type="datetime", nullable=true)
    * @Gedmo\Timestampable(on="create")
    * @var \DateTime
    */
    protected $createdAt;

    /**
    * @ORM\Column(type="datetime", nullable=true)
    * @Gedmo\Timestampable(on="update")
    * @var \DateTime
    */
    protected $editedAt;

    /**
    * @ORM\Column(type="datetime", nullable=true)
    * @var \DateTime
    */
    protected $deletedAt;


    public function getId() {
         return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getCreatedAt() {
         return $this->createdAt;
    }

    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getEditedAt() {
         return $this->editedAt;
    }

    public function setEditedAt($editedAt) {
        $this->editedAt = $editedAt;
        return $this;
    }

    public function getDeletedAt() {
         return $this->deletedAt;
    }

    public function setDeletedAt($deletedAt) {
        $this->deletedAt = $deletedAt;
        return $this;
    }

}
