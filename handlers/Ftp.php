<?php

namespace phptools\handlers;


class Ftp{

    private $file;
    private $conn;
    private $server;
    private $login;
    private $remoteFile;
    private $app;

    public function __construct($server, $app)
    {
        $this->server = $server;
        //$this->conn = $conn;
        $this->conn = ftp_connect($server) or die("Could not connect to $server");
        $this->login = ftp_login($this->conn, CONFIG['ftp.user'], CONFIG['ftp.password']);
        $this->app = $app;
    }

    public function localFile($file){
        $this->file = $file;
        return $this;
    }

    public function remoteFile($file){
        $this->remoteFile = $file;
        return $this;
    }

    public function upload(){
        //$ftp_server = "ftp.fcmsoft.com";
        if (ftp_put($this->conn, $this->remoteFile, $this->file, FTP_ASCII)) {
            $this->app->log("successfully uploaded $this->file\n");
            echo "successfully uploaded $this->file\n";

        } else {
            $this->app->log("There was a problem while uploading $this->file\n");
            echo "There was a problem while uploading $this->file\n";
        }

        return $this;
    }

    public function deleteRemoteFiles(){
        // try to delete $file
        $file_list = ftp_nlist($this->conn, ".");

        foreach($file_list as $file){
            if(strpos($file, ".sql")){
                echo "....Deleting $file \n";
                if (ftp_delete($this->conn, $file)) {
                    $this->app->log("$file deleted successful\n");
                   echo "$file deleted successful\n";
                } else {
                    $this->app->log("could not delete $file\n");
                    echo "could not delete $file\n";
                }
            }
        }
    }

    public function close(){
        ftp_close($this->conn);
        return $this;
    }

}