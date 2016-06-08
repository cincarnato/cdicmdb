<?php

namespace CdiCmdb\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class MainController extends AbstractActionController {

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    public function setEntityManager(EntityManager $em) {
        $this->em = $em;
    }

    public function getEntityManager() {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }
        return $this->em;
    }

    public function typeAction() {

        $grid = $this->getServiceLocator()->get('cdiGrid');
        $source = new \CdiDataGrid\DataGrid\Source\Doctrine($this->getEntityManager(), '\CdiCmdb\Entity\CiType');
        $grid->setSource($source);
        $grid->setRecordPerPage(20);
        $grid->datetimeColumn('createdAt', 'Y-m-d H:i:s');
        $grid->datetimeColumn('updatedAt', 'Y-m-d H:i:s');
        $grid->datetimeColumn('expiration', 'Y-m-d H:i:s');
        $grid->hiddenColumn('createdAt');
        $grid->hiddenColumn('updatedAt');
        $grid->hiddenColumn('createdBy');
        $grid->hiddenColumn('lastUpdatedBy');

        $grid->addExtraColumn("<i class='fa fa-book ' ></i>", "<a class='btn btn-primary fa fa-book' href='/cdicmdb/main/abm/{{id}}' target='_blank'></a>", "right", false);
        
        $grid->setTableClass("table-condensed customClass");

        $grid->prepare();
        return array('grid' => $grid);
    }

    public function abmAction() {
        $id = $this->params("id");
        $query = $this->getEntityManager()->createQueryBuilder()
                ->select('u')
                ->from('CdiCmdb\Entity\CiType', 'u')
                ->where("u.id = :id")
                ->setParameter("id", $id);
        $ciType = $query->getQuery()->getOneOrNullResult();

        $entityName = $ciType->getName();

        if ($ciType) {


            $query = $this->getEntityManager()->createQueryBuilder()
                    ->select('u')
                    ->from('CdiEntity\Entity\Entity', 'u')
                    ->where("u.name = :name")
                    ->setParameter("name", $entityName);
            $entity = $query->getQuery()->getOneOrNullResult();
        }


        $grid = $this->getServiceLocator()->get('cdiGrid');
        $source = new \CdiDataGrid\DataGrid\Source\Doctrine($this->getEntityManager(), $entity->getFullName());
        $grid->setSource($source);
        $grid->setRecordPerPage(100);
        $grid->datetimeColumn('createdAt', 'Y-m-d H:i:s');
        $grid->datetimeColumn('updatedAt', 'Y-m-d H:i:s');
        $grid->datetimeColumn('expiration', 'Y-m-d H:i:s');
        $grid->hiddenColumn('createdAt');
        $grid->hiddenColumn('updatedAt');
        $grid->hiddenColumn('createdBy');
        $grid->hiddenColumn('lastUpdatedBy');


        foreach ($entity->getProperties() as $property) {
            if ($property->getType() == "oneToMany") {
                $grid->hiddenColumn($property->getName());
                $grid->addExtraColumn("<i class='fa fa-tree ' >" . $property->getName() . "</i>", "<a class='btn btn-warning fa fa-tree' href='/cdicmdb/main/onetomany/" . $entity->getId() . "/{{id}}/" . $property->getRelatedEntity()->getId() . "' target='_blank'></a>", "right", false);
            }

            if ($property->getType() == "manyToOne" || $property->getType() == "oneToOne") {
                $filterType = new \DoctrineModule\Form\Element\ObjectSelect();
                $filterType->setOptions(array(
                    'object_manager' => $this->getEntityManager(),
                    'target_class' => $property->getRelatedEntity()->getFullName(),
                    'display_empty_item' => true,
                    'empty_item_label' => 'Todos',
                ));
                $grid->setFormFilterSelect($property->getRelatedEntity()->getName(), $filterType);
                $grid->clinkColumn($property->getName(), array(array("path" => '/cdicmdb/main/view/', "data" => $property->getRelatedEntity()->getName()), array("path" => '/' . $property->getRelatedEntity()->getId(), "data" => "")));
            }
            if ($property->getType() == "file") {
                $grid->fileColumn($property->getName(), $property->getWebpath(), "50px", "30px");
            }
        }



        $grid->addExtraColumn("View", "<a class='btn btn-success fa fa-binoculars' href='/cdicmdb/main/view/{{id}}/" . $entity->getId() . "' target='_blank'></a>", "left", false);
        $grid->addExtraColumn("Graph", "<a class='btn btn-warning fa fa-image' href='/cdicmdb/main/graph/{{id}}/" . $entity->getId() . "' target='_blank'></a>", "left", false);

        $grid->addEditOption("Edit", "left", "btn btn-primary fa fa-edit");
        $grid->addDelOption("Del", "left", "btn btn-danger fa fa-trash");
        $grid->addNewOption("Add", "btn btn-primary fa fa-plus", " Agregar");
        $grid->setTableClass("table-condensed customClass");

        $grid->prepare();
        return array('grid' => $grid, 'entity' => $entity);
    }

    public function viewAction() {
        //name
        $id = $this->params("id");
        //ID of Entity to filter and select
        $eid = $this->params("eid");

        $query = $this->getEntityManager()->createQueryBuilder()
                ->select('u')
                ->from('CdiEntity\Entity\Entity', 'u')
                ->where("u.id = :id")
                ->setParameter("id", $eid);
        $rentity = $query->getQuery()->getOneOrNullResult();

        $query = $this->getEntityManager()->createQueryBuilder()
                ->select('u')
                ->from($rentity->getFullName(), 'u')
                ->where("u.id = :id")
                ->setParameter("id", $id);

        $entity = $query->getQuery()->getOneOrNullResult();
        return array('entity' => $entity, 'rentity' => $rentity);
    }

    public function graphAction() {
        //name
        $id = $this->params("id");
        //ID of Entity to filter and select
        $eid = $this->params("eid");



        return array('id' => $id, 'eid' => $eid);
    }

    public function jsonAction() {
        $id = $this->params("id");
        //ID of Entity to filter and select
        $eid = $this->params("eid");

        $query = $this->getEntityManager()->createQueryBuilder()
                ->select('u')
                ->from('CdiEntity\Entity\Entity', 'u')
                ->where("u.id = :id")
                ->setParameter("id", $eid);
        $rentity = $query->getQuery()->getOneOrNullResult();

        $query = $this->getEntityManager()->createQueryBuilder()
                ->select('u')
                ->from($rentity->getFullName(), 'u')
                ->where("u.id = :id")
                ->setParameter("id", $id);

        $entity = $query->getQuery()->getOneOrNullResult();

        $a["name"] = $entity->__toString();

        $nodos = array();
        foreach ($rentity->getProperties() as $property) {

            if ($property->getType() == "oneToMany") {

                //Agrego el tipo de elemento asociado
                $nodos[$property->getName()]["name"] = $property->getName();
                $nodos[$property->getName()]["children"] = array();

                $method = "get" . ucfirst($property->getName());


                $childs = $entity->{$method}();
                if ($childs) {
                    foreach ($childs as $child) {
                        $p["name"] = "";
                         if(is_a($child,"CdiCmdb\Entity\\cron")){
                             $p["name"] .= " ".$child->getMinuto()." ".$child->getHora()." ".$child->getDiames()." ".$child->getMes()." ".$child->getDiasemana()." ";
                        }
                        
                        $p["name"] .= $child->__toString();
                        if(is_a($child,"CdiCmdb\Entity\\interfaz")){
                             $p["name"] .= " ".$child->getIp();
                        }
                        
                         

                        $nodos[$property->getName()]["children"][] = $p;
                    }
                }
            }
        }

        foreach ($nodos as $nodo) {
            $a["children"][] = $nodo;
        }
        $result = new JsonModel($a);
        return $result;
    }

    public function onetomanyAction() {

        //name
        $id = $this->params("id");
        //ID of Entity to filter and select
        $eid = $this->params("eid");
        //Related Entity
        $rid = $this->params("rid");


        $query = $this->getEntityManager()->createQueryBuilder()
                ->select('u')
                ->from('CdiEntity\Entity\Entity', 'u')
                ->where("u.id = :id")
                ->setParameter("id", $id);
        $entity = $query->getQuery()->getOneOrNullResult();


        $query = $this->getEntityManager()->createQueryBuilder()
                ->select('u')
                ->from('CdiEntity\Entity\Entity', 'u')
                ->where("u.id = :id")
                ->setParameter("id", $rid);
        $rentity = $query->getQuery()->getOneOrNullResult();



        $query = $this->getEntityManager()->createQueryBuilder()
                ->select('u')
                ->from($rentity->getFullName(), 'u')
                ->where("u." . $entity->getName() . " = :id")
                ->setParameter("id", $eid);


        $grid = $this->getServiceLocator()->get('cdiGrid');
        $source = new \CdiDataGrid\DataGrid\Source\Doctrine($this->getEntityManager(), $rentity->getFullName(), $query);
        $grid->setSource($source);
        $grid->setRecordPerPage(20);
        $grid->datetimeColumn('createdAt', 'Y-m-d H:i:s');
        $grid->datetimeColumn('updatedAt', 'Y-m-d H:i:s');
        $grid->datetimeColumn('expiration', 'Y-m-d H:i:s');
        $grid->hiddenColumn('createdAt');
        $grid->hiddenColumn('updatedAt');
        $grid->hiddenColumn('createdBy');
        $grid->hiddenColumn('lastUpdatedBy');


        $grid->addEditOption("Edit", "left", "btn btn-success fa fa-edit");
        $grid->addDelOption("Del", "left", "btn btn-warning fa fa-trash");
        $grid->addNewOption("Add", "btn btn-primary fa fa-plus", " Agregar");
        $grid->setTableClass("table-condensed customClass");

        $grid->prepare();



        if ($this->request->getPost("crudAction") == "edit" || $this->request->getPost("crudAction") == "add") {
            $grid->getEntityForm()->get($entity->getName())->setValue($eid);
        }


        return array('grid' => $grid, 'entity' => $rentity);
    }

}
