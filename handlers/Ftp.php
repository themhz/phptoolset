<?php

namespace phptools\handlers;


class Ftp{

    private $file;
    private $conn;
    private $server;
    private $login;
    private $remoteFile;

    public function __construct($server)
    {
        $this->server = $server;
        //$this->conn = $conn;
        $this->conn = ftp_connect($server) or die("Could not connect to $server");
        $this->login = ftp_login($this->conn, CONFIG['ftp.user'], CONFIG['ftp.password']);
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
            echo "successfully uploaded $this->file\n";
        } else {
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
                   echo "$file deleted successful\n";
                } else {
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