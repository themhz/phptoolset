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

namespace phptools\core;

use phptools\handlers\FileSystem;
use phptools\handlers\Ftp;
use phptools\handlers\DatabaseManager;
use phptools\handlers\Xamp;

class App
{
    public $config = CONFIG;
    public $rootpath;

    public function __construct($rootpath)
    {
        $this->rootpath = $rootpath;
        //echo __DIR__;
//        echo $this->rootpath.'\phptoolset\logs\log_'.date("j.n.Y").'.log';
//        die();

    }

    /**
     *    Basic Control flow of tasks that will be executed when the application runs
     * ----------------------------------------------------------------------------------
     * 1. Load requested HTTP Method [get,post,etc..] and fields from the request or messagebody
     * 2. Authenticate the user request get userData and paths and set user session variables
     * 3. Route the user to the corresponding controller
     *
     * @return void
     */
    public function start()
    {
        $dbManager = new DatabaseManager($this);
        global $argv;

        if(!empty($argv[1])){
            switch ($argv[1]){
                case 'database':
                    if(!empty($argv[2])){
                        switch ($argv[2]){
                            case 'export':
                                $dbManager->exportDumps();
                                echo 'doing export';
                                break;
                            case 'drop':
                                echo "doing drop..\n";
                                $dbManager->dropTables();
                                break;
                            case 'restore':
                                echo "doing restore..\n";
                                $dbManager->restoreDumps(CONFIG['dumpfiles']);
                                break;
                            case 'upload':
                                echo "uploading\n";
                                $dbManager->uploadToFtp();
                                break;
                            case 'deleteremote':
                                echo 'deleting remote files';
                                $ftp= new Ftp(CONFIG['ftp.host'], $this);
                                $ftp->deleteRemoteFiles();
                                break;
                            case 'deletelocal':
                                echo 'deleting local files';
                                $fileSystem = new FileSystem(CONFIG['dumpfiles'], $this);
                                print_r($fileSystem->dirdelete());
                                break;
                            case 'backup':
                                echo 'doing backup';
                                $this->log("Doing backup  \n");
                                $fileSystem = new FileSystem(CONFIG['dumpfiles'], $this);
                                $fileSystem->dirdelete();
                                $ftp= new Ftp(CONFIG['ftp.host'], $this);
                                $ftp->deleteRemoteFiles();

                                $dbManager->exportDumps();
                                $dbManager->uploadToFtp();
                                $this->log("--------------------------end backup-----------------------\n");
                                break;
                            default:
                                $this->showdatabasemenu();
                        }
                    }else{
                        $this->showdatabasemenu();
                    }
                    break;
                case 'xamp':
                    if(!empty($argv[2])) {
                        switch ($argv[2]) {
                            case 'backup':
                                echo 'doing apache files backup';
                                $xamp = new Xamp();
                                $xamp->backup("C:\\xampp\\htdocs", "C:\\Users\\themhz\\Documents\\codebase\\", $this);

                                break;
                        }
                    }
                    break;
                case 'virtuemart':
                    if(!empty($argv[2])) {
                        switch ($argv[2]) {
                            case 'images':
                                switch ($argv[3]) {
                                    case 'copy':
                                        echo "from ".$argv[4]. " to ". $argv[5];
                                        $xamp = new Xamp();
                                        $xamp->backup($argv[4], $argv[5], $this);
                                        break;
                                }
//                                echo 'doing apache files backup';
//                                $xamp = new Xamp();
//                                $xamp->backup("C:\\xampp\\htdocs", "C:\\Users\\themhz\\Documents\\codebase\\", $this);

                                break;
                        }
                    }
                    break;
                default:
                    $this->showmenu();
            }

        }else{
            $this->showmenu();
        }
    }

    public function showdatabasemenu(){

        echo "Please an option {database backup,database export,database drop,database restore}";
    }

    public function showmenu(){

        echo "Please an option {backup, export, drop, restore}";
    }

    public function log($log){
        file_put_contents($this->rootpath.'\phptoolset\logs\log_'.date("j.n.Y").'.log', $log, FILE_APPEND);

    }


}