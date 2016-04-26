<?php

return array(
    'view_manager' => array(
        'template_path_stack' => array(
            'cdicalendar' => __DIR__ . '/../view',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'CdiCalendar\Controller\Calendar' => 'CdiCalendar\Controller\CalendarController',
            'cdiagenda' => 'CdiCalendar\Controller\AgendaController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'cdiagenda' => array(
                'type' => 'Literal',
                'priority' => 1000,
                'options' => array(
                    'route' => '/cdiagenda',
                    'defaults' => array(
                        'controller' => 'agenda',
                        'action' => 'index',
                    ),
                ),
                'child_routes' => array(
                    'list' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/list[/:p]',
                            'defaults' => array(
                                'controller' => 'zfcuseradmin',
                                'action' => 'list',
                            ),
                        ),
                    ),
                    'create' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/create',
                            'defaults' => array(
                                'controller' => 'zfcuseradmin',
                                'action' => 'create'
                            ),
                        ),
                    ),
                    'edit' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/edit/:calendarId',
                            'defaults' => array(
                                'controller' => 'zfcuseradmin',
                                'action' => 'edit',
                                'userId' => 0
                            ),
                        ),
                    ),
                    'remove' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/remove/:calendarId',
                            'defaults' => array(
                                'controller' => 'zfcuseradmin',
                                'action' => 'remove',
                                'userId' => 0
                            ),
                        ),
                    ),
                ),
            ),
            'cdicalendar' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/cdicalendar[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'CdiCalendar\Controller\Calendar',
                        'action' => 'abm',
                    ),
                ),
            ),
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'cdicalendar_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => __DIR__ . '/../src/CdiCalendar/Entity',
            ),
            'orm_default' => array(
                'drivers' => array(
                    'CdiCalendar\Entity' => 'cdicalendar_entity',
                ),
            ),
        ),
    ),
    'cdicalendar_options' => array(
        'calendarEntityClass' => 'CdiCalendar/Entity/Calendar',
    )
);
