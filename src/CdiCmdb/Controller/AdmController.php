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

        $grid->addExtraColumn("<i class='fa fa-commenting-o ' ></i>", "<a class='btn btn-warning fa fa-commenting-o' href='/cdicmdb/adm/etype/{{id}}'></a>", "left", false);
        $grid->addEditOption("Edit", "left", "btn btn-success fa fa-edit");
        //$grid->addDelOption("Del", "left", "btn btn-warning fa fa-trash");
        $grid->addNewOption("Add", "btn btn-primary fa fa-plus", " Agregar");
        $grid->setTableClass("table-condensed customClass");

        $grid->prepare();
        return array('grid' => $grid);
    }

    public function etypeAction() {
        $id = $this->params("id");
        //tiene que verificar si existe la entidad, hacer reflection en tal caso, cargar las propiedades
        //1.Harcodeo el namespace y path, a fin decuenas tiene que quedar en este vendor

        $namespace = "CdiCmdb\Entity";
        $path = __DIR__ . "/Entity";


        $query = $this->getEntityManager()->createQueryBuilder()
                ->select('u')
                ->from('CdiCmdb\Entity\CiType', 'u')
                ->where("u.id = :id")
                ->setParameter("id", $id);
        $ciType = $query->getQuery()->getOneOrNullResult();

        if ($ciType) {
            $query = $this->getEntityManager()->createQueryBuilder()
                    ->select('u')
                    ->from('CdiEntity\Entity\Entity', 'u')
                    ->where("u.id = :id")
                    ->setParameter("id", $ciType->getName());
            $entity = $query->getQuery()->getOneOrNullResult();
        }

        if (!$entity) {

            $namespaces = new \CdiEntity\Entity\Namespaces();
            $namespaces->setName($namespace);
            $namespaces->setPath($path);

            $entity = new \CdiEntity\Entity\Entity();
            $entity->setName($namespace . $ciType->getName());
            $entity->setPath($path);
            $entity->setNamespace($namespaces);

            $this->getEntityManager()->persist($namespaces);
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

        $grid->addExtraColumn("<i class='fa fa-commenting-o ' ></i>", "<a class='btn btn-warning fa fa-commenting-o' href='/cdicmdb/adm/etype/{{id}}'></a>", "left", false);
        $grid->addEditOption("Edit", "left", "btn btn-success fa fa-edit");
        //$grid->addDelOption("Del", "left", "btn btn-warning fa fa-trash");
        $grid->addNewOption("Add", "btn btn-primary fa fa-plus", " Agregar");
        $grid->setTableClass("table-condensed customClass");

        $grid->prepare();


        if ($this->request->getPost("crudAction") == "edit" || $this->request->getPost("crudAction") == "add") {
            $grid->getEntityForm()->get("entity")->setValue($entity->getId());
       
        }

        return array('grid' => $grid);
    }

}
