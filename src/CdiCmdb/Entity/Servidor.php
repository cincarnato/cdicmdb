<?php

namespace CdiCmdb\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * @ORM\Entity
 * @ORM\Table(name="cmdb_ci_servidor")
 * @author Cristian Incarnato
 */
class Servidor extends \CdiCommons\Entity\BaseEntity {

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Type("Zend\Form\Element\Hidden")
     */
    protected $id;

    /**
     * @var string
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Options({"label":"Name:"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":100}})
     * @ORM\Column(type="string", length=100, unique=true, nullable=true, name="name")
     */
    protected $name;

    public function __construct() {
        
    }

    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    public function __toString() {
        return $this->name;
    }

}
