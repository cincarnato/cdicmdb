<?php

namespace CdiCmdb\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class ConsoleController extends AbstractActionController {

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

    public function donowAction() {
        $request = $this->getRequest();

        // Make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (!$request instanceof \Zend\Console\Request) {
            throw new \RuntimeException('You can only use this action from a console!');
        }

        // Get system service name  from console and check if the user used --verbose or -v flag
        $doname = $request->getParam('doname', false);
        $verbose = $request->getParam('verbose');

        $shell = "ps aux";
        if ($doname) {
            $shell .= " |grep -i $doname ";
        }
        $shell .= " > /var/www/crm/app/data/success.txt ";
        //execute...
        system($shell, $val);

        if (!$verbose) {
            echo "Process listed in /var/www/crm/app/data/success.txt \r\n";
        } else {
            $file = fopen('/var/www/crm/app/data/success.txt', "r");

            while (!feof($file)) {
                $listprocess = trim(fgets($file));

                echo $listprocess . "\r\n";
            }
            fclose($file);
        }
    }

    public function scanAction() {
        $request = $this->getRequest();

        // Make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (!$request instanceof \Zend\Console\Request) {
            throw new \RuntimeException('You can only use this action from a console!');
        }

        // Get system service name  from console and check if the user used --verbose or -v flag
        $host = $request->getParam('host', false);
        $port = $request->getParam('port', false);
        $verbose = $request->getParam('verbose');

        if (!$port) {
            $port = 22;
        }

        $credentials = array("ssh" => array("root" => "palermonights!@#77"),
            "mysql" => array("root" => "disgaea2")
        );

        $scanner = new \CdiCmdb\Scanner\Linux($host, $port, $credentials);
        $result = $scanner->start();

        if (!$verbose) {
            var_dump($result);
            //  echo json_encode($result,JSON_PRETTY_PRINT );
        } else {
            //   var_dump($result);
            echo json_encode($result, JSON_PRETTY_PRINT);
        }
    }

}
