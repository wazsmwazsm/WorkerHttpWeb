<?php
use Framework\Route;

Route::get('/a/b', function() {
    echo 'a';
});

Route::get('/a/c', "App\Controller\TestController@test");
