<?php
use Framework\Route;

Route::get('/a/b', function() {
    echo 'a';
});
Route::get('/a/c', "App\Controller\TestController@test");

Route::get('/test', function() {
    return 'test';
});

Route::get('test2/', function() {
    return 'test2';
});

Route::group(['prefix' => '/pre', 'namespace' => 'App\Controller'], function() {
    Route::get('control/', 'TestController@test');
    Route::post('call1/', function() {
        return 'hello1';
    });
    Route::get('call2/', function() {
        return 'hello2';
    });
});

Route::get('test3/', function() {
    return '2333';
});
