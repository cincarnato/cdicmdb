<?php

namespace CdiCmdb\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class AdmController extends AbstractActionController {

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
        $grid->addExtraColumn("<i class='fa fa-bars ' ></i>", "<a class='btn btn-warning fa fa-bars' href='/cdicmdb/adm/etype/{{id}}' target='_blank'></a>", "left", false);
        $grid->addEditOption("Edit", "left", "btn btn-success fa fa-edit");
        //$grid->addDelOption("Del", "left", "btn btn-warning fa fa-trash");
        $grid->addNewOption("Add", "btn btn-primary fa fa-plus", " Agregar");
        $grid->setTableClass("table-condensed customClass");

        $grid->prepare();
        return array('grid' => $grid);
    }

    public function generateAction() {
        $id = $this->params("id");
        $service = new \CdiEntity\Service\CodeGenerator();


        $query = $this->getEntityManager()->createQueryBuilder()
                ->select('u')
                ->from('CdiEntity\Entity\Entity', 'u')
                ->where("u.id = :id")
                ->setParameter("id", $id);
        $entity = $query->getQuery()->getOneOrNullResult();

        return array("class" => $service->update($entity, $updateSchema));
    }

    public function etypeAction() {
        $id = $this->params("id");
        //tiene que verificar si existe la entidad, hacer reflection en tal caso, cargar las propiedades
        //1.Harcodeo el namespace y path, a fin decuenas tiene que quedar en este vendor

        $namespace = "CdiCmdb\Entity";
        $path = dirname(__DIR__) . "/Entity";


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


        if (!$entity) {

            $namespaces = new \CdiEntity\Entity\Namespaces();
            $namespaces->setName($namespace);
            $namespaces->setPath($path);
            $this->getEntityManager()->persist($namespaces);
            $this->getEntityManager()->flush();

            $entity = new \CdiEntity\Entity\Entity();
            $entity->setName($entityName);
            $entity->setPath($path);
            $entity->setNamespace($namespaces);


            $this->getEntityManager()->persist($entity);
            $this->getEntityManager()->flush();
        } 

        $query = $this->getEntityManager()->createQueryBuilder()
                ->select('u')
                ->from('CdiEntity\Entity\Property', 'u')
                ->where("u.entity = :id")
                ->setParameter("id", $entity);




        $grid = $this->getServiceLocator()->get('cdiGrid');
        $source = new \CdiDataGrid\DataGrid\Source\Doctrine($this->getEntityManager(), '\CdiEntity\Entity\Property', $query);
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
            $grid->getEntityForm()->get("entity")->setValue($entity->getId());
        }
        
            $updateEntity = $this->getServiceLocator()->get('cdientity_generate_entity');
        $exec = $updateEntity->update($entity, true);

        if (preg_match("/Database\sschema\supdated/", $exec)) {
            $result = true;
        } else if (preg_match("/error/", $exec)) {
            $result = false;
        } else if (preg_match("/Nothing\sto\supdate/", $exec)) {
            $result = null;
        }

        return array('grid' => $grid,'entity' => $entity,'result'=> $result,"exec" => $exec);
    }

}
