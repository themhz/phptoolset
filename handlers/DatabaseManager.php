<?php

namespace phptools\handlers;
use \PDO;
use phptools\handlers\Database;
use phptools\handlers\Ftp;

class DatabaseManager{

    private $db;
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function uploadToFtp(){
        $ftp = new Ftp(CONFIG["ftp.host"]);

        $dir    = CONFIG["dumpfiles"];
        $files = scandir($dir);

        foreach ($files as $file) {
            if (strpos($file, '.sql') !== false) {
                $fileparts = explode("-", $file);

                if (!in_array($fileparts[0], array("information_schema", "mysql", "performance_schema"))) {
                    //echo $file."\n";
                    $ftp->localFile($dir.$file)->remoteFile($file);
                    $ftp->upload();
                }
                sleep(2);

            }
        }
        $ftp->close();

    }

    public function exportDumps(){
        $date = new \DateTime();

        $db = Database::getInstance();
        $sql = "SHOW DATABASES";
        $sth = $db->dbh->prepare($sql);
        $sth->execute();
        $results = $sth->fetchAll(\PDO::FETCH_OBJ);
        $location = CONFIG["dumpfiles"];
        foreach($results as $schema){
            echo "backing up ".$schema->Database."\n";
            $database = $schema->Database;
            if(!in_array($database, array("information_schema", "mysql", "performance_schema"))){
                exec(CONFIG["mysqlexe"]."mysqldump --user=root --password= --result-file=$location".$database."-".$date->format('Ymd_H_i_s').".sql $schema->Database");
            }
        }

    }

    public function dropTables(){

        $sql = "SHOW DATABASES";
        $sth = $this->db->dbh->prepare($sql);
        $sth->execute();
        $results = $sth->fetchAll(\PDO::FETCH_OBJ);

        foreach($results as $schema){
            if(!in_array($schema->Database, array("information_schema", "mysql", "performance_schema"))){
                echo "dropping ".$schema->Database."\n";
                $database = $schema->Database;
                $sql = "DROP DATABASE $database";
                $sth = $this->db->dbh->prepare($sql);
                $sth->execute();
            }
        }
    }

    public function restoreDumps($dir){

        $date = new \DateTime();
        $db = Database::getInstance();

        $files1 = scandir($dir);

        foreach ($files1 as $file){
            if(strpos($file, '.sql') !== false){
                $fileparts = explode("-", $file);
                echo "Creating schema for ".$fileparts[0]."\n";
                if(!in_array($fileparts[0], array("information_schema", "mysql", "performance_schema"))){
                    $sql = "CREATE DATABASE ".$fileparts[0];
                    $sth = $this->db->dbh->prepare($sql);
                    $sth->execute();

                    $mysqlDatabaseName =$fileparts[0];
                    $mysqlUserName =CONFIG['db.user'];
                    $mysqlPassword =CONFIG['db.password'];;
                    $mysqlHostName =CONFIG['db.host'];
                    $mysqlImportFilename =$dir.$file;

                    $command=CONFIG['mysqlexe'].'mysql -h' .$mysqlHostName .' -u' .$mysqlUserName .' ' .$mysqlDatabaseName .' < ' .$mysqlImportFilename;
                    exec($command,$output,$worked);
                    switch($worked){
                        case 0:
                            echo 'The data from the file <b>' .$mysqlImportFilename .'</b> were successfully imported into the database <b>' .$mysqlDatabaseName .'</b>';
                            break;
                        case 1:
                            echo 'An error occurred during the import. Please check if the file is in the same folder as this script. Also check the following data again:<br/><br/><table><tr><td>MySQL Database Name:</td><td><b>' .$mysqlDatabaseName .'</b></td></tr><tr><td>MySQL User Name:</td><td><b>' .$mysqlUserName .'</b></td></tr><tr><td>MySQL Password:</td><td><b>NOTSHOWN</b></td></tr><tr><td>MySQL Host Name:</td><td><b>' .$mysqlHostName .'</b></td></tr><tr><td>MySQL Import Dateiname:</td><td><b>' .$mysqlImportFilename .'</b></td></tr></table>';
                            break;
                    }
                    unlink($mysqlImportFilename);
                }

            }
        }
    }
}