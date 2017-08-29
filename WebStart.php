<?php

require_once __DIR__ . '/vendor/autoload.php';
use Workerman\Worker;
use App\Controller\TestController;
use Framework\Route;
use Framework\Requests;


$http_worker = new Worker('http://0.0.0.0:600');
$http_worker->count = 4;
$http_worker->onMessage = function($con, $data) {

    Route::get('/a/b', function() {
        echo 'a';
    });

    Route::get('/a/c', "App\Controller\TestController@test");
    $rst = Route::dispatch();

    $con->send($rst);
};

Worker::runAll();
