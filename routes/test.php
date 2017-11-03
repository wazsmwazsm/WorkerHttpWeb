<?php
use Framework\Http\Route;

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



Route::group(['prefix' => '/g1', 'namespace' => 'App'], function() {
    Route::group(['prefix' => '/g2', 'namespace' => 'Controller'], function() {
        Route::get('test', function() {
            return 'g1 g2 test success';
        });
        Route::get('con', "TestController@test");
    });

    Route::get('test', function() {
        return 'g1 test success';
    });
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
