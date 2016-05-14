<?php

namespace CdiCmdb\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Zend\Form\Annotation;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cmdb_java")
 */
class java
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
     * @ORM\Column(type="string", length=50, unique=false, nullable=true,
     * name="nombre")
     */
    public $nombre = null;

    /**
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"locacion"})
     * @ORM\Column(type="string", length=300, unique=false, nullable=true,
     * name="locacion")
     */
    public $locacion = null;

    /**
     * @Annotation\Type("DoctrineModule\Form\Element\ObjectSelect")
     * @Annotation\Options({"label":"servidor:","empty_option":
     * "","target_class":"CdiCmdb\Entity\servidor"})
     * @ORM\ManyToOne(targetEntity="CdiCmdb\Entity\servidor")
     */
    public $servidor = null;

    /**
     * @Annotation\Type("DoctrineModule\Form\Element\ObjectSelect")
     * @Annotation\Options({"label":"solucion:","empty_option":
     * "","target_class":"CdiCmdb\Entity\solucion"})
     * @ORM\ManyToOne(targetEntity="CdiCmdb\Entity\solucion")
     */
    public $solucion = null;

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

    public function getLocacion()
    {
        return $this->locacion;
    }

    public function setLocacion($locacion)
    {
        $this->locacion = $locacion;
    }

    public function getServidor()
    {
        return $this->servidor;
    }

    public function setServidor($servidor)
    {
        $this->servidor = $servidor;
    }

    public function getSolucion()
    {
        return $this->solucion;
    }

    public function setSolucion($solucion)
    {
        $this->solucion = $solucion;
    }


}

