<?php

namespace phptools\handlers;
use phptools\handlers\FileSystem;

class Wamp
{

    public function backup($path, $destpath, $app){
        $files = new FileSystem($path, $app);
        $files->copydirs($path, $destpath);
    }

}