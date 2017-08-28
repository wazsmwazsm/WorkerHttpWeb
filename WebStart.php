<?php

require_once __DIR__ . '/vendor/autoload.php';
use Workerman\Worker;
use App\Controller\TestController;
use Framework\Route;

$http_worker = new Worker('http://0.0.0.0:600');
$http_worker->count = 4;
$http_worker->onMessage = function($con, $data) {
    $test = new TestController();
    // var_dump($_SERVER);
    // var_dump(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    Route::get('/a/b', function() {
        echo 'a';
    });
    var_dump(Route::dispatch());
    // var_dump(Route::$map_tree);
    // $con->send($test->test());
};

Worker::runAll();
