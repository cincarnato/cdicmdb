<?php

return array(
    'view_manager' => array(
        'template_path_stack' => array(
            'cdicmdb' => __DIR__ . '/../view',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'CdiCmdb\Controller\Adm' => 'CdiCmdb\Controller\AdmController',
            'CdiCmdb\Controller\Main' => 'CdiCmdb\Controller\MainController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'cdicmdb' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/cdicmdb/adm/[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'CdiCmdb\Controller\Adm',
                        'action' => 'type',
                    ),
                ),
            ),
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'cdicmdb_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => __DIR__ . '/../src/CdiCalendar/Entity',
            ),
            'orm_default' => array(
                'drivers' => array(
                    'CdiCmdb\Entity' => 'cdicmdb_entity',
                ),
            ),
        ),
    ),
    'cdicmdb_options' => array(
    )
);
