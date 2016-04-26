<?php

//use Zend\ServiceManager\ServiceLocatorInterface;

return array(
    'aliases' => array(
        'calendar_doctrine_em' => 'Doctrine\ORM\EntityManager',
    ),
    'invokables' => array(
        'cdicalendar_calendar_service' => 'CdiCalendar\Service\Calendar',
    ),
    'factories' => array(
        'cdicalendar_options' => function ($sm) {
            $config = $sm->get('Config');
            return new \CdiCalendar\Options\CalendarOptions(isset($config['cdicalendar_options']) ? $config['cdicalendar_options'] : array());
        },
                'cdicalendar_factory' => 'CdiCalendar\Service\Factory\CalendarFactory',
                'cdicalendar_calendar_mapper' => function ($sm) {
            return new \CdiCalendar\Mapper\CalendarDoctrine(
                    $sm->get('calendar_doctrine_em'), $sm->get('cdicalendar_options')
            );
        },'cdicalendar_calendar_form_min' => function ($sm) {
            $formCalendar = new \CdiCalendar\Form\Calendar($sm);
            $formCalendar->addMin();
            return $formCalendar;
        },
                
                
            ),
        );
        