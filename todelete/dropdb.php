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
    if(!in_array($schema->Database, array("information_schema", "mysql", "performance_schema"))){
        echo "dropping ".$schema->Database."\n";
        $database = $schema->Database;
        $sql = "DROP DATABASE $database";
        $sth = $db->dbh->prepare($sql);
        $sth->execute();
    }
}