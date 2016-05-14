<?php

namespace CdiCmdb\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Zend\Form\Annotation;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cmdb_servidor")
 */
class servidor
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
     * @Annotation\Options({"label":"hostname"})
     * @ORM\Column(type="string", length=50, unique=false, nullable=true,
     * name="hostname")
     */
    public $hostname = null;

    /**
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"distro"})
     * @ORM\Column(type="string", length=30, unique=false, nullable=true,
     * name="distro")
     */
    public $distro = null;

    /**
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"distrorelease"})
     * @ORM\Column(type="string", length=20, unique=false, nullable=true,
     * name="distrorelease")
     */
    public $distrorelease = null;

    /**
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"arquitectura"})
     * @ORM\Column(type="string", length=10, unique=false, nullable=true,
     * name="arquitectura")
     */
    public $arquitectura = null;

    /**
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"cpu"})
     * @ORM\Column(type="string", length=50, unique=false, nullable=true, name="cpu")
     */
    public $cpu = null;

    /**
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"procesadores"})
     * @ORM\Column(type="integer", length=2, unique=false, nullable=true,
     * name="procesadores")
     */
    public $procesadores = null;

    /**
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"memoria"})
     * @ORM\Column(type="string", length=10, unique=false, nullable=true,
     * name="memoria")
     */
    public $memoria = null;

    /**
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"systemproduct"})
     * @ORM\Column(type="string", length=30, unique=false, nullable=true,
     * name="systemproduct")
     */
    public $systemproduct = null;

    /**
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"tipo"})
     * @ORM\Column(type="string", length=20, unique=false, nullable=true, name="tipo")
     */
    public $tipo = null;

    /**
     * @Annotation\Exclude()
     * @ORM\OneToMany(targetEntity="CdiCmdb\Entity\interfaz", mappedBy="servidor")
     */
    public $interfaces = null;

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
        return (string) $this->hostname;
    }

    public function getHostname()
    {
        return $this->hostname;
    }

    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
    }

    public function getDistro()
    {
        return $this->distro;
    }

    public function setDistro($distro)
    {
        $this->distro = $distro;
    }

    public function getDistrorelease()
    {
        return $this->distrorelease;
    }

    public function setDistrorelease($distrorelease)
    {
        $this->distrorelease = $distrorelease;
    }

    public function getArquitectura()
    {
        return $this->arquitectura;
    }

    public function setArquitectura($arquitectura)
    {
        $this->arquitectura = $arquitectura;
    }

    public function getCpu()
    {
        return $this->cpu;
    }

    public function setCpu($cpu)
    {
        $this->cpu = $cpu;
    }

    public function getProcesadores()
    {
        return $this->procesadores;
    }

    public function setProcesadores($procesadores)
    {
        $this->procesadores = $procesadores;
    }

    public function getMemoria()
    {
        return $this->memoria;
    }

    public function setMemoria($memoria)
    {
        $this->memoria = $memoria;
    }

    public function getSystemproduct()
    {
        return $this->systemproduct;
    }

    public function setSystemproduct($systemproduct)
    {
        $this->systemproduct = $systemproduct;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    public function getInterfaces()
    {
        return $this->interfaces;
    }

    public function setInterfaces($interfaces)
    {
        $this->interfaces = $interfaces;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }


}

