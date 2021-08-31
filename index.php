<?php

use phptools\core\App;

require 'vendor/autoload.php';
require 'config.php';
$time_start = hrtime(true);
$app = new App(dirname(__DIR__));
$app->start();
$time_end = hrtime(true);
$time = ($time_end - $time_start);
echo ("Process executed in ".round($time/1e+6/1000))." seconds";
