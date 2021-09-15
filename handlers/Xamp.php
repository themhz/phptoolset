<?php

namespace phptools\handlers;
use phptools\handlers\FileSystem;

class Xamp
{

    public function backup($path, $destpath, $app){
        $files = new FileSystem($path, $app);
        $files->copydirs($path, $destpath);
    }

    public function move($path, $destpath, $app){
        rename($path, $destpath);
    }

}