<?php

/* 
 * Copyright (C) 2021 themhz
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace phptools\handlers;
use \PDO;
use \DateTime;

//This class uses singleton method to access the database used by the application
class Database
{
    private static $instance;
    public $user;
    public $password;
    public $dbhost;
    public $basename = null;
    public $dbh;

    private function __construct()
    {        
        $this->user = CONFIG['db.user'];
        $this->password = CONFIG['db.password'];
        $this->dbhost = CONFIG['db.host'];
        $this->basename = $this->getBaseName();
        $this->dbh = new PDO("mysql:host=$this->dbhost;dbname=$this->basename", $this->user, $this->password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8;",PDO::ATTR_PERSISTENT => true));

        // $now = new DateTime();
        // $mins = $now->getOffset() / 60;
        // $sgn = ($mins < 0 ? -1 : 1);
        // $mins = abs($mins);
        // $hrs = floor($mins / 60);
        // $mins -= $hrs * 60;
        // $offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);
        // $this->dbh->exec("SET time_zone='$offset';");
          
        $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function setBaseName($basename=CONFIG['db.name']){
        $this->basename = $basename;
        $this->dbh = new PDO("mysql:host=$this->dbhost;dbname=$this->basename", $this->user, $this->password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8;",PDO::ATTR_PERSISTENT => true));
        $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getBaseName(){
        if($this->basename == null){
            return CONFIG['db.name'];
        }else{
            return $this->basename;
        }
    }
    public static function getInstance()
    {

        if (!isset(self::$instance))
        {
            $object = __CLASS__;
            self::$instance = new $object;
        }

        return self::$instance;
    }
}

