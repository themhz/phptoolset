<?php
require("../handlers/Ftp.php");

$ftp = new Ftp("ftp.fcmsoft.com");
$dir    = 'C:\wamp64\www\phptoolset\database\dumps\\';
$files = scandir($dir);

foreach ($files as $file) {
    if (strpos($file, '.sql') !== false) {
        $fileparts = explode("-", $file);

        if (!in_array($fileparts[0], array("information_schema", "mysql", "performance_schema"))) {
            //echo $file."\n";
            $ftp->localFile("../database/dumps/$file")->remoteFile($file);
            $ftp->upload();
        }
        sleep(2);

    }
}
$ftp->close();

