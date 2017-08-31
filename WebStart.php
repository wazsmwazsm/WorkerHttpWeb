<?php

require_once __DIR__ . '/bootstrap/boot.php';
use Workerman\Worker;
use Framework\App;

// echo error message on daemon mode
Worker::$stdoutFile = './tmp/log/error.log';

$http_worker = new Worker('http://0.0.0.0:600');
$http_worker->count = 4;
$http_worker->onMessage = function($con, $data) {
    // run web app
    App::run($con, $data);
};

Worker::runAll();
