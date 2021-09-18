<?php

namespace phptools\handlers;

class LogHandler
{
    public $file;
    public function __construct($file)
    {
        $this->file;
    }

    public function log($log){
        file_put_contents($this->file.'_'.date("j.n.Y").'.log', $log, FILE_APPEND);

    }



}
