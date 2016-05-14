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

        $credentials = require $request->getParam('credentials', false);
        $cidr = $request->getParam('hosts', false);
        $port = $request->getParam('port', false);
        $verbose = $request->getParam('verbose');


        if (!$port) {
            $port = 22;
        }

        $hosts = $this->getEachIpInRange($cidr);
        var_dump($hosts);
        foreach ($hosts as $host) {
            $scanner = new \CdiCmdb\Scanner\Linux($host, $port, $credentials);
            $result = $scanner->start();

            if (is_array($result)) {
                $this->todb($result);
            }

            if (!$verbose) {
                var_dump($result);
                //  echo json_encode($result,JSON_PRETTY_PRINT );
            } else {
                var_dump($result);
                //echo json_encode($result, JSON_PRETTY_PRINT);
            }
        }
    }

    function todb($result) {
        $query = $this->getEntityManager()->createQueryBuilder()
                ->select('u')
                ->from('CdiCmdb\Entity\servidor', 'u')
                ->where("u.hostname = :hostname")
                ->setParameter("hostname", $result["hostname"]);

        $servidor = $query->getQuery()->getOneOrNullResult();

        if (!$servidor) {
            $servidor = new \CdiCmdb\Entity\servidor();
            $servidor->setHostname($result["hostname"]);
            $servidor->setSystemproduct($result["system"]);
            $servidor->setArquitectura($result["arquitectura"]);
            $servidor->setProcesadores($result["procesadores"]);
            $servidor->setCpu($result["cpu"]);
            $servidor->setMemoria($result["memoria"]);
            $servidor->setDistro($result["distro"]);
            $servidor->setDistrorelease($result["release"]);
            $servidor->setTipo($result["type"]);
            $this->getEntityManager()->persist($servidor);
            $this->getEntityManager()->flush();
        }

        if (is_array($result["interfaces"])) {
            foreach ($result["interfaces"] as $interface) {

                $query = $this->getEntityManager()->createQueryBuilder()
                        ->select('u')
                        ->from('CdiCmdb\Entity\interfaz', 'u')
                        ->where("u.nombre = :nombre")
                        ->andWhere('u.servidor = :servidor')
                        ->setParameter("nombre", $interface["name"])
                        ->setParameter("servidor", $servidor);

                $interfaz = $query->getQuery()->getOneOrNullResult();

                if (!$interfaz) {
                    $interfaz = new \CdiCmdb\Entity\interfaz();
                    $interfaz->setNombre($interface["name"]);
                    $interfaz->setServidor($servidor);
                }
                $interfaz->setMac($interface["mac"]);
                $interfaz->setIp($interface["ip"]);
                $interfaz->setMask($interface["mask"]);

                $this->getEntityManager()->persist($interfaz);
            }
        }


        if (is_array($result["crones"])) {
            foreach ($result["crones"] as $crones) {

                $query = $this->getEntityManager()->createQueryBuilder()
                        ->select('u')
                        ->from('CdiCmdb\Entity\cron', 'u')
                        ->where("u.comando = :command")
                        ->andWhere('u.servidor = :servidor')
                        ->setParameter("command", $crones["command"] . $crones["argument"])
                        ->setParameter("servidor", $servidor);

                $cron = $query->getQuery()->getOneOrNullResult();
                if (!$cron) {
                    $cron = new \CdiCmdb\Entity\cron();
                    $cron->setComando($crones["command"] . $crones["argument"]);
                    $cron->setServidor($servidor);
                }
                $cron->setMinuto($crones["minuto"]);
                $cron->setHora($crones["hora"]);
                $cron->setDiames($crones["diames"]);
                $cron->setMes($crones["mes"]);
                $cron->setDiasemana($crones["diasemana"]);


                $this->getEntityManager()->persist($cron);
            }
        }

        if (is_array($result["java"])) {
            foreach ($result["java"] as $javas) {

                $query = $this->getEntityManager()->createQueryBuilder()
                        ->select('u')
                        ->from('CdiCmdb\Entity\java', 'u')
                        ->where("u.nombre = :nombre")
                        ->andWhere('u.servidor = :servidor')
                        ->setParameter("nombre", $javas["name"])
                        ->setParameter("servidor", $servidor);

                $java = $query->getQuery()->getOneOrNullResult();
                if (!$java) {
                    $java = new \CdiCmdb\Entity\java();
                    $java->setNombre($javas["name"]);
                    $java->setServidor($servidor);
                }
                $java->setLocacion($javas["locate"]);

                $this->getEntityManager()->persist($java);
            }
        }

        if ($result["mysql"]["version"]) {

            $query = $this->getEntityManager()->createQueryBuilder()
                    ->select('u')
                    ->from('CdiCmdb\Entity\db', 'u')
                    ->andWhere('u.servidor = :servidor')
                    ->setParameter("servidor", $servidor);

            $db = $query->getQuery()->getOneOrNullResult();

            if (!$db) {
                $db = new \CdiCmdb\Entity\db();
                $db->setServidor($servidor);
            }
            $db->setVersion($result["mysql"]["version"]);
            $this->getEntityManager()->persist($db);


            if (is_array($result["mysql"]["dbs"])) {
                foreach ($result["mysql"]["dbs"] as $schema) {

                    $query = $this->getEntityManager()->createQueryBuilder()
                            ->select('u')
                            ->from('CdiCmdb\Entity\esquema', 'u')
                            ->andWhere('u.nombre = :nombre')
                            ->andWhere('u.servidor = :servidor')
                            ->setParameter("nombre", $schema)
                            ->setParameter("servidor", $servidor);

                    $esquema = $query->getQuery()->getOneOrNullResult();
                    if (!$esquema) {
                        $esquema = new \CdiCmdb\Entity\esquema();
                        $esquema->setServidor($servidor);
                        $esquema->setDb($db);
                    }

                    $esquema->setNombre($schema);


                    $this->getEntityManager()->persist($esquema);
                }
            }
        }

        if ($result["webserver"]["software"]) {

            $query = $this->getEntityManager()->createQueryBuilder()
                    ->select('u')
                    ->from('CdiCmdb\Entity\webserver', 'u')
                    ->andWhere('u.servidor = :servidor')
                    ->setParameter("servidor", $servidor);
            $webserver = $query->getQuery()->getOneOrNullResult();

            if (!$webserver) {
                $webserver = new \CdiCmdb\Entity\webserver();
                $webserver->setServidor($servidor);
            }

            $webserver->setSoftware($result["webserver"]["software"]);
            $this->getEntityManager()->persist($webserver);
        }



        $this->getEntityManager()->flush();
    }

    function getIpRange($cidr) {

        list($ip, $mask) = explode('/', $cidr);

        $maskBinStr = str_repeat("1", $mask) . str_repeat("0", 32 - $mask);      //net mask binary string
        $inverseMaskBinStr = str_repeat("0", $mask) . str_repeat("1", 32 - $mask); //inverse mask

        $ipLong = ip2long($ip);
        $ipMaskLong = bindec($maskBinStr);
        $inverseIpMaskLong = bindec($inverseMaskBinStr);
        $netWork = $ipLong & $ipMaskLong;

        if ($mask != 32) {
            $start = $netWork + 1; //ignore network ID(eg: 192.168.1.0)
            $end = ($netWork | $inverseIpMaskLong) - 1; //ignore brocast IP(eg: 192.168.1.255)
        } else {
            $start = $netWork; //ignore network ID(eg: 192.168.1.0)
            $end = $netWork; //ignore brocast IP(eg: 192.168.1.255)
        }

        return array('firstIP' => $start, 'lastIP' => $end);
    }

    function getEachIpInRange($cidr) {
        $ips = array();
        $range = $this->getIpRange($cidr);
        var_dump($range);
        for ($ip = $range['firstIP']; $ip <= $range['lastIP']; $ip++) {
            $ips[] = long2ip($ip);
        }
        return $ips;
    }

    public function verazAction() {
        $request = $this->getRequest();

        // Make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (!$request instanceof \Zend\Console\Request) {
            throw new \RuntimeException('You can only use this action from a console!');
        }

        // Get system service name  from console and check if the user used --verbose or -v flag

        $credentials = require $request->getParam('credentials', false);
        $verbose = $request->getParam('verbose');


        $scanner = new \CdiCmdb\Scanner\Veraz($credentials);
        $result = $scanner->start();

        if (is_array($result)) {
            $this->todb($result);
        }

        if (!$verbose) {
            var_dump($result);
            //  echo json_encode($result,JSON_PRETTY_PRINT );
        } else {
            var_dump($result);
            //echo json_encode($result, JSON_PRETTY_PRINT);
        }
    }

}
