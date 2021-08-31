<?php

use phptools\core\App;

require 'vendor/autoload.php';
require 'config.php';

$app = new App(dirname(__DIR__));
$app->start();

//// connect and login to FTP server
//$ftp_server = "ftp.fcmsoft.com";
//$ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
//$login = ftp_login($ftp_conn, "hal9000@fcmsoft.com", "ethe526996####");
//
//// get file list of current directory
//$file_list = ftp_nlist($ftp_conn, ".");
//var_dump($file_list);
//
//// close connection
//ftp_close($ftp_conn);