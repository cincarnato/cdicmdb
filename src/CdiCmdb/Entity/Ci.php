<?php

namespace CdiCmdb\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="cmdb_ci")
 *
 * @author Cristian Incarnato
 */
class Ci extends \CdiCommons\Entity\BaseEntity {

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

    /**
     * @Annotation\Type("DoctrineModule\Form\Element\ObjectSelect")
     * @Annotation\Options({
     * "label":"Entity:",
     * "empty_option": "",
     * "target_class":"CdiCmdb\Entity\CiType",
     * "property": "name"})
     * @ORM\ManyToOne(targetEntity="CdiCmdb\Entity\CiType")
     * @ORM\JoinColumn(name="ci_type_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $type;

    /**
     * @var 
     * @ORM\OneToMany(targetEntity="CdiCmdb\Entity\Ci", mappedBy="entity")
     */
    protected $cis;

    public function __construct() {
        $this->cis = new ArrayCollection();
    }

    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getType() {
        return $this->type;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setType($type) {
        $this->type = $type;
    }

    function getCis() {
        return $this->cis;
    }

    function setCis($cis) {
        $this->cis = $cis;
    }

    function addCis($cis) {
        $this->cis[] = $cis;
    }

    public function __toString() {
        return $this->name;
    }

}
