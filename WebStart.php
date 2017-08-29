<?php

require_once __DIR__ . '/bootstrap/boot.php';
use Workerman\Worker;
use Framework\Route;
use Framework\Config;

$http_worker = new Worker('http://0.0.0.0:600');
$http_worker->count = 4;
$http_worker->onMessage = function($con, $data) {
    // var_dump(Config::$config);
    var_dump(Config::get('app.debug'));
    // var_dump(Config::get('database.mysql'));
    $rst = Route::dispatch();
    $con->send($rst);
};

Worker::runAll();
