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
class Linux {

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

    public function __construct($host, $port, array $credentials, $timeout = 10) {
        $this->host = $host;
        $this->port = $port;
        $this->credentials = $credentials;
        $this->timeout = $timeout;
    }

    public function start() {
        $this->initLogger();
        if ($this->connect()) {
            $this->basic();
            $this->interfaces();
            $this->apache();
            $this->javas();
            $this->crones();
            $this->mysql();
            $this->ssh->disconnect();
            return $this->return;
        } else {
            return $this->host . " no se pudo scanear";
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

    protected function basic() {
        //Hostname
        $this->return["hostname"] = trim($this->ssh->exec('hostname'));
        usleep(10000);
        //Uptime
        $this->return["uptime"] = trim($this->ssh->exec('uptime'));
        //Distro y Release
        $lsb = trim($this->ssh->exec('lsb_release -a'));
        if (preg_match("/Description:.*\n/", $lsb, $m)) {
            $this->return["distro"] = trim(preg_replace("/Description:/", "", $m[0]));
        }

        if (preg_match("/Release:.*\n/", $lsb, $m)) {
            $this->return["release"] = trim(preg_replace("/Release:/", "", $m[0]));
        }


        //Arquitectura
        $uname = trim($this->ssh->exec('uname -a'));
        if (preg_match("/x86_64/", $uname)) {
            $this->return["arquitectura"] = "64 bit";
        }
        if (preg_match("/i386|i686/", $uname)) {
            $this->return["arquitectura"] = "32 bit";
        }

        //Procesadores (Ver lscpu mas compacto, toda la info...)
        $procC = trim($this->ssh->exec('cat /proc/cpuinfo |grep  "processor"'));
        $countP = preg_match_all("/processor/", $procC);
        $this->return["procesadores"] = $countP;
        $procM = trim($this->ssh->exec('cat /proc/cpuinfo |grep -m 1 "model name"'));
        if (preg_match("/model\sname/i", $procM)) {
            $this->return["cpu"] = trim(preg_replace("/model\sname\s*:/i", "", $procM));
        }

        //Memoria
        $mem = trim($this->ssh->exec('free -m'));
        if (preg_match("/Mem:.*\n/", $mem, $m)) {
            preg_match_all("/\d+/", $m[0], $matches);
            $this->return["memoria"] = trim($matches[0][0]) . "M";
            $this->return["memoriaUsada"] = trim($matches[0][1]) . "M";
            $this->return["memoriaLibre"] = trim($matches[0][2]) . "M";
        }

        //Discos.. Uff...
        $disk = trim($this->ssh->exec('lsblk'));
        if (preg_match_all("/.*disk/", $disk, $ms)) {
            $u = 1;
            foreach ($ms[0] as $d) {
                preg_match("/sd.|hd.|xvd./", $d, $n);
                $this->return["disk"][$u]["name"] = $n[0];
                preg_match("/\d*(T|G|M)/", $d, $s);
                $this->return["disk"][$u]["size"] = $s[0];
                $u++;
            }
        }

        //Verificar si es virtual 
        $systemName = trim($this->ssh->exec('dmidecode -s system-product-name'));
        $this->return["system"] = $systemName;

        if (preg_match("/vmware|xen/i", $systemName)) {
            $this->return["type"] = "virtual";
        } else {
            $this->return["type"] = "dedicado";
        }
    }

    protected function interfaces() {

        $regexIp = "[0-9]*.[0-9]*.[0-9]*.[0-9]*";
        $ifconfig = trim($this->ssh->exec('ifconfig'));

        //Separo interfaces
        if (preg_match_all("/.*\s*Link\s*encap.*\n.*Mask:.*/", $ifconfig, $matchs)) {
            $u = 1;
            foreach ($matchs[0] as $value) {
                //Nombre interfaz
                if (preg_match("/.*\s*Link\s*encap/", $value, $m)) {
                    $val = preg_replace("/\s*Link\s*encap/", "", $m[0]);

                    $this->return["interfaces"][$u]["name"] = $val;
                }

                //MAC
                if (preg_match("/HWaddr\s*.*\n/", $value, $m)) {
                    $val = preg_replace("/HWaddr\s*/", "", $m[0]);
                    $this->return["interfaces"][$u]["mac"] = trim($val);
                }

                //IP
                if (preg_match("/inet\saddr:$regexIp/", $value, $m)) {
                    $val = preg_replace("/inet\saddr:/", "", $m[0]);
                    $this->return["interfaces"][$u]["ip"] = trim($val);
                }


                //MASK
                if (preg_match("/Mask:$regexIp/", $value, $m)) {
                    $val = preg_replace("/Mask:/", "", $m[0]);
                    $this->return["interfaces"][$u]["mask"] = trim($val);
                }



                $u++;
            }
        } else {
            $this->logger->warn('Sin Concordancia en regex interfaces ' . $i);
        }

        //$this->return["ifconfig"] = $ifconfig;
    }

    protected function apache() {
        $apache = trim($this->ssh->exec('apachectl -v'));

        if (preg_match("/Server version:.*/", $apache, $m)) {
            $this->return["webserver"]["software"] = trim(preg_replace("/Server\sversion:/", "", $m[0]));

            //Virtual host  apachectl -S
        } else {
            $apache = trim($this->ssh->exec('httpd -v'));
            if (preg_match("/Server version:.*/", $apache, $m)) {
                $this->return["webserver"]["software"] = trim(preg_replace("/Server\sversion:/", "", $m[0]));
                //Virtual host  httpd -S
            }
        }
    }

    protected function javas() {
        $javas = trim($this->ssh->exec('ps aux|grep jar|grep -v "grep" '));
        if (preg_match_all("/\s\w*\.jar/i", $javas, $ms)) {
            $u = 1;
            foreach ($ms[0] as $j) {
                
                
                $this->return["java"][$u]["name"] = trim($j);

                //Locate
                $locate = trim($this->ssh->exec('locate ' . trim($j)));
                $this->return["java"][$u]["locate"] = $locate;
                $u++;
            }
        }
    }

    protected function mysql() {
        //1. Credencial libre
        if (!$this->mysqlLocal()) {
            //2.Credenciales posibles
            if (!$this->mysqlCredential()) {
                echo "No se puede loguear al mysql";
            }
        }
    }

    protected function mysqlLocal() {
        $version = trim($this->ssh->exec('mysql -e "SELECT @@version\G"'));
        if (preg_match("/@@version.*/", $version, $m)) {
            $this->return["mysql"]["version"] = trim(preg_replace("/@@version:/", "", $m[0]));

            $dbs = trim($this->ssh->exec('mysql -e "show databases\G"'));
            if (preg_match_all("/Database.*/", $dbs, $ms)) {
                foreach ($ms[0] as $value) {
                    $this->return["mysql"]["dbs"][] = trim(preg_replace("/Database:/", "", $value));
                }
            }
            return true;
        } else {
            return false;
        }
    }

    protected function mysqlCredential() {

        foreach ($this->credentials["mysql"] as $credential) {
            $c = "-u" . $credential["user"] . " -p" . $credential["pass"];
            $version = trim($this->ssh->exec('mysql ' . $c . ' -e "SELECT @@version\G"'));
            if (preg_match("/@@version.*/", $version, $m)) {
                $this->return["mysql"]["version"] = trim(preg_replace("/@@version:/", "", $m[0]));

                $dbs = trim($this->ssh->exec('mysql ' . $c . ' -e "show databases\G"'));
                if (preg_match_all("/Database.*/", $dbs, $ms)) {
                    foreach ($ms[0] as $value) {
                        $this->return["mysql"]["dbs"][] = trim(preg_replace("/Database:/", "", $value));
                    }
                }
                return true;
            }
        }

        return false;
    }

    protected function crones() {

        $crones = trim($this->ssh->exec('crontab -l'));

        $result = preg_match_all(
                "/\n(\*|[0-5]?[0-9]|\*\/[0-9]+)\s+" //minuto
                . "(\*|1?[0-9]|2[0-3]|\*\/[0-9]+)\s+" //hora
                . "(\*|[1-2]?[0-9]|3[0-1]|\*\/[0-9]+)\s+"  //DiaMes
                . "(\*|[0-9]|1[0-2]|\*\/[0-9]+|jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)\s+" //mes
                . "(\*\/[0-9]+|\*|[0-7]|sun|mon|tue|wed|thu|fri|sat)\s*" //diaSemana
                . ".*\n?/", $crones, $matchs);

        //Separo Crones
        if ($result) {

            $u = 1;
            foreach ($matchs[0] as $value) {
                $explode = preg_split("/\s+/", trim($value));
                foreach ($explode as $key => $val) {

                    switch ($key) {
                        case 0:
                            $this->return["crones"][$u]["minuto"] = $val;
                            break;
                        case 1:
                            $this->return["crones"][$u]["hora"] = $val;
                            break;
                        case 2:
                            $this->return["crones"][$u]["diames"] = $val;
                            break;
                        case 3:
                            $this->return["crones"][$u]["mes"] = $val;
                            break;
                        case 4:
                            $this->return["crones"][$u]["diasemana"] = $val;
                            break;
                        case 5:
                            $this->return["crones"][$u]["command"] = $val;
                            break;
                        case 6:
                            if (preg_match("/php|sh|bash/", $this->return["crones"][$u]["command"])) {
                                $this->return["crones"][$u]["command"] .=" " . $val;
                            } else {
                                $this->return["crones"][$u]["argument"] .= " " . $val;
                            }
                            break;
                        default :

                            $this->return["crones"][$u]["argument"] .= " " . $val;
                            break;
                    }
                }

                $u++;
            }
        }
    }

}
