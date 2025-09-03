<?php

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

define('BASEPATH', realpath(__DIR__.'/../') .DS);

require __DIR__.'/../vendor/autoload.php';

$npds = require_once 'app' .DS .'Bootstrap' .DS .'Bootstrap.php';

dump(
    'hello npds mvc run', 
    $npds,
    $npds->basePath()
);