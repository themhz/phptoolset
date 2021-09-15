<?php

namespace phptools\database;

use mywebshop\components\handlers\Database;
require("../config.php");
require("../handlers/Database.php");

$date = new \DateTime();

$db = Database::getInstance();
$sql = "SHOW DATABASES";
//
$sth = $db->dbh->prepare($sql);
$sth->execute();
$results = $sth->fetchAll(\PDO::FETCH_OBJ);

foreach($results as $schema){
    echo "backing up ".$schema->Database."\n";
    $database = $schema->Database;
    if(!in_array($database, array("information_schema", "mysql", "performance_schema"))){
        exec("C:\\xampp\\mysql\\bin\\mysqldump --user=root --password= --result-file=\"C:\\xampp\\htdocs\\phptoolset\\database\\dumps\\".$database."-".$date->format('Ymd_H_i_s').".sql\" $schema->Database");
    }
}

