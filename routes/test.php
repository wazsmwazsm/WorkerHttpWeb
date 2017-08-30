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

// Route::group(['prefix' => '/aa'], function() {
//
// });
