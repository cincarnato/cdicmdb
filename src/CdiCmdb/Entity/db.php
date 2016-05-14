<?php

namespace CdiCmdb\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Zend\Form\Annotation;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cmdb_db")
 */
class db
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Type("Zend\Form\Element\Hidden")
     */
    public $id = null;

    /**
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"version"})
     * @ORM\Column(type="string", length=30, unique=false, nullable=true,
     * name="version")
     */
    public $version = null;

    /**
     * @Annotation\Type("DoctrineModule\Form\Element\ObjectSelect")
     * @Annotation\Options({"label":"servidor:","empty_option":
     * "","target_class":"CdiCmdb\Entity\servidor"})
     * @ORM\ManyToOne(targetEntity="CdiCmdb\Entity\servidor")
     */
    public $servidor = null;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function __toString()
    {
        return (string) $this->version;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setVersion($version)
    {
        $this->version = $version;
    }

    public function getServidor()
    {
        return $this->servidor;
    }

    public function setServidor($servidor)
    {
        $this->servidor = $servidor;
    }


}

