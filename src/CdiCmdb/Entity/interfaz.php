<?php

namespace CdiCmdb\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Zend\Form\Annotation;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cmdb_interfaz")
 */
class interfaz
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
     * @Annotation\Options({"label":"nombre"})
     * @ORM\Column(type="string", length=20, unique=false, nullable=true,
     * name="nombre")
     */
    public $nombre = null;

    /**
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"mac"})
     * @ORM\Column(type="string", length=30, unique=false, nullable=true, name="mac")
     */
    public $mac = null;

    /**
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"ip"})
     * @ORM\Column(type="string", length=30, unique=false, nullable=true, name="ip")
     */
    public $ip = null;

    /**
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"mask"})
     * @ORM\Column(type="string", length=30, unique=false, nullable=true, name="mask")
     */
    public $mask = null;

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
        return (string) $this->nombre;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function getMac()
    {
        return $this->mac;
    }

    public function setMac($mac)
    {
        $this->mac = $mac;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    public function getMask()
    {
        return $this->mask;
    }

    public function setMask($mask)
    {
        $this->mask = $mask;
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

