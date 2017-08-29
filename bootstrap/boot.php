<?php

use Framework\Config;

// autoload
require_once __DIR__ . '/../vendor/autoload.php';

// config
$configs = preg_grep('/.*\.php$/', scandir(__DIR__ . '/../config'));

foreach ($configs as $config) {
    Config::$config[basename($config, '.php')] = (require_once __DIR__ . '/../config/'. $config);
}

// routers
require_once __DIR__ . '/../routes/test.php';
