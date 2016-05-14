<?php

namespace CdiCmdb\Scanner;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;

/**
 * Description of Linux
 *
 * @author Cristian Incarnato <cristian.cdi@gmail.com>
 */
class Veraz {

    protected $logger;
    protected $ssh;
    protected $host;
    protected $port = 22;
    protected $timeout = 30;
    protected $credentials = array();
    protected $alive = true;
    protected $filename = "/var/www/cmdb/app/data/log/scanner.log";
    protected $maxFiles = 5;
    protected $level = Logger::DEBUG;
    protected $return;
    protected $test = true;

    public function __construct($credentials) {
        $this->credentials = $credentials;
    }

    public function start() {
        $this->initLogger();

        $this->ivrs("192.168.9.28");
        $this->ivrs("192.168.9.69");
     //   $this->lnx21();
       // $this->lnx13();
    }

    protected function test($file) {
        if ($this->test) {
            $file .=".test";
        }
        return $file;
    }

    protected function ivrs($ip) {
        $this->host = $ip;
        $this->port = "22";
        if ($this->connect()) {

            #Primera Maniobra
            $file = '/usr/IVR/AsteriskService/conf/configVirtualIVR.cfg';
            $file = $this->test($file);
            $replace = "wsfrontera.sondeosglobal.com";
            $new = "ws1veraz.snd.int";
            $result = $this->ssh->exec("sed -i 's/" . $replace . "/" . $new . "/g' " . $file);
            echo $result;


            #Segunda Maniobra
            $file = '/usr/IVR/AsteriskService/conf/configFaxServer.cfg';
            $file = $this->test($file);
            $replace = "wsfrontera.sondeosglobal.com";
            $new = "ws1veraz.snd.int";
            $result = $this->ssh->exec("sed -i 's/" . $replace . "/" . $new . "/g' " . $file);
            echo $result;


            #Tercera Maniobra
            $file = '/usr/IVR/AsteriskService/conf/configBaseDatosVirualIvr.cfg';
            $file = $this->test($file);
            $replace = "192.168.9.149";
            $new = "192.168.34.118";
            $result = $this->ssh->exec("sed -i 's/" . $replace . "/" . $new . "/g' " . $file);
            echo $result;


            #Cuarta Maniobra
            $file = '/usr/IVR/AsteriskService/configVerazII/configVerazII.cfg';
            $file = $this->test($file);
            $replace = "wsfrontera2.sondeosglobal.com";
            $new = "ws2veraz.snd.int";
            $result = $this->ssh->exec("sed -i 's/" . $replace . "/" . $new . "/g' " . $file);
            echo $result;




            $this->ssh->disconnect();
        } else {
            return $this->host . " no se pudo conectar" . $ip;
        }
    }

    public function lnx21() {
        $this->host = "192.168.9.160";
        $this->port = "22";
        if ($this->connect()) {

            #Primera Maniobra
            $file = '/script/verazivr/cron_habilita_derivaciones_veraz.php';
            $file = $this->test($file);
            $replace = "192.168.9.149";
            $new = "192.168.34.118";
            $result = $this->ssh->exec("sed -i 's/" . $replace . "/" . $new . "/g' " . $file);
            echo $result;

            #segunda Maniobra
            $file = '/script/veraz/verazalertas/common/Datos.php';
            $file = $this->test($file);
            $replace = "192.168.9.149";
            $new = "192.168.34.118";
            $result = $this->ssh->exec("sed -i 's/" . $replace . "/" . $new . "/g' " . $file);
            echo $result;


            $this->ssh->disconnect();
        } else {
            return $this->host . " no se pudo conectar lnx21";
        }
    }
    
        public function lnx13() {
        $this->host = "192.168.9.39";
        $this->port = "22";
        if ($this->connect()) {

            #Primera Maniobra
            $file = '/script/verazivr/cron_habilita_derivaciones_veraz.php';
            $file = $this->test($file);
            $replace = "192.168.9.149";
            $new = "192.168.34.118";
            $result = $this->ssh->exec("sed -i 's/" . $replace . "/" . $new . "/g' " . $file);
            echo $result;

            #segunda Maniobra
            $file = '/script/veraz/verazalertas/common/Datos.php';
            $file = $this->test($file);
            $replace = "192.168.9.149";
            $new = "192.168.34.118";
            $result = $this->ssh->exec("sed -i 's/" . $replace . "/" . $new . "/g' " . $file);
            echo $result;


            $this->ssh->disconnect();
        } else {
            return $this->host . " no se pudo conectar lnx21";
        }
    }

    protected function initLogger() {
        // Create the logger
        $this->logger = new Logger('scan');
// Now add some handlers
        $handler = new RotatingFileHandler($this->filename, $this->maxFiles, $this->level);
        $this->logger->pushHandler($handler);
        $this->logger->info('Init Logger');
    }

    public function connect() {
        $this->ssh = new \phpseclib\Net\SSH2($this->host, $this->port, $this->timeout);
        $i = 1;
        foreach ($this->credentials["ssh"] as $credential) {
            $result = $this->login($credential["user"], $credential["pass"]);
            if ($result) {
                $this->logger->info(' Login exitoso credencial numero ' . $i . $user . $pass . ' IP:' . $this->host);
                return true;
                break;
            } else {
                $this->logger->warn(' Login fallido credencial numero ' . $i . $user . $pass);
            }

            $i++;
        }
        if (!$result) {
            $this->logger->error($i . ' Fallo de login con todas las credenciales suministradas IP:' . $this->host);
            $this->alive = false;
            return false;
        }
    }

    protected function login($user, $pass) {
        try {
            if (!$this->ssh->login($user, $pass)) {
                return false;
            } else {
                return true;
            }
        } catch (\Exception $ex) {
            $this->logger->error('Excepcion en login');
        } catch (\RuntimeException $ex) {
            $this->logger->error('RuntimeExcepcion en login');
        }
    }

}
