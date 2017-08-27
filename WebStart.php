<?php

require_once __DIR__ . '/vendor/autoload.php';
use Workerman\Worker;
use App\Controller\TestController;

$http_worker = new Worker('http://0.0.0.0:600');
$http_worker->count = 4;
$http_worker->onMessage = function($con, $data) {
    $test = new TestController();
    $con->send($test->test());
};

Worker::runAll();
