<?php

namespace CdiCmdb;


use Zend\ModuleManager\Feature\AutoloaderProviderInterface,
         Zend\ModuleManager\Feature\ConsoleUsageProviderInterface,
           Zend\Console\Adapter\AdapterInterface as Console;

/**
 * Module
 *
 * @package   Cdi
 * @copyright Cristian Incarnato (c) - http://www.cincarnato.com
 */
class Module implements AutoloaderProviderInterface, ConsoleUsageProviderInterface {

    public function init() {
        
    }
  
    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig() {
            return include __DIR__ . '/config/services.config.php';
    }

     public function getConsoleUsage(Console $console)
    {
    return array(
            // Describe available commands
            'get happen [--verbose|-v] <doname>'    => 'Get Process already happen',
 
            // Describe expected parameters
            array( 'doname',            'Process Name' ),
            array( '--verbose|-v',     '(optional) turn on verbose mode'        ),
 
    );
    }

}
