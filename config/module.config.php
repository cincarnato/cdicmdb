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
            'CdiCmdb\Controller\Console' => 'CdiCmdb\Controller\ConsoleController'
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'get-happen-use' => array(
                    'options' => array(
                        // add [ and ] if optional ( ex : [<doname>] )
                        'route' => 'get happen [--verbose|-v] <doname>',
                        'defaults' => array(
                            '__NAMESPACE__' => 'CdiCmdb\Controller',
                            'controller' => 'Console',
                            'action' => 'donow'
                        ),
                    ),
                ),
                'scan' => array(
                    'options' => array(
                        // add [ and ] if optional ( ex : [<doname>] )
                        'route' => 'scan [--verbose|-v] <host> [<port>]',
                        'defaults' => array(
                            '__NAMESPACE__' => 'CdiCmdb\Controller',
                            'controller' => 'Console',
                            'action' => 'scan'
                        ),
                    ),
                ),
            )
        )
    ),
    'router' => array(
        'routes' => array(
            'cdicmdbadm' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/cdicmdb/adm[/:action][/:id]',
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
            'cdicmdbmain' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/cdicmdb/main[/:action][/:id][/:eid][/:rid]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'CdiCmdb\Controller\Main',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'cdicmdb_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => __DIR__ . '/../src/CdiCmdb/Entity',
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
