<?php

namespace CdiCmdb\Controller;

use Zend\Mvc\Controller\AbstractActionController;

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
        }

        $grid->addExtraColumn("View", "<a class='btn btn-success fa fa-binoculars' href='/cdicmdb/main/view/{{id}}/" . $entity->getId() . "' target='_blank'></a>", "left", false);

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
