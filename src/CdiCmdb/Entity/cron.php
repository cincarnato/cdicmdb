<?php

namespace CdiCmdb\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Zend\Form\Annotation;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cmdb_cron")
 */
class cron
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
     * @Annotation\Options({"label":"comando"})
     * @ORM\Column(type="string", length=500, unique=false, nullable=true,
     * name="comando")
     */
    public $comando = null;

    /**
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"minuto"})
     * @ORM\Column(type="string", length=5, unique=false, nullable=true, name="minuto")
     */
    public $minuto = null;

    /**
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"hora"})
     * @ORM\Column(type="string", length=5, unique=false, nullable=true, name="hora")
     */
    public $hora = null;

    /**
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"diames"})
     * @ORM\Column(type="string", length=5, unique=false, nullable=true, name="diames")
     */
    public $diames = null;

    /**
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"mes"})
     * @ORM\Column(type="string", length=5, unique=false, nullable=true, name="mes")
     */
    public $mes = null;

    /**
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"diasemana"})
     * @ORM\Column(type="string", length=5, unique=false, nullable=true,
     * name="diasemana")
     */
    public $diasemana = null;

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
        return (string) $this->comando;
    }

    public function getComando()
    {
        return $this->comando;
    }

    public function setComando($comando)
    {
        $this->comando = $comando;
    }

    public function getMinuto()
    {
        return $this->minuto;
    }

    public function setMinuto($minuto)
    {
        $this->minuto = $minuto;
    }

    public function getHora()
    {
        return $this->hora;
    }

    public function setHora($hora)
    {
        $this->hora = $hora;
    }

    public function getDiames()
    {
        return $this->diames;
    }

    public function setDiames($diames)
    {
        $this->diames = $diames;
    }

    public function getMes()
    {
        return $this->mes;
    }

    public function setMes($mes)
    {
        $this->mes = $mes;
    }

    public function getDiasemana()
    {
        return $this->diasemana;
    }

    public function setDiasemana($diasemana)
    {
        $this->diasemana = $diasemana;
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

