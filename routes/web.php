<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['middleware' => 'cors'], function ($router) {

    // $route→httpmethod('/path', 'NamaControlle r@method');
    // $router->get('/stuff', 'StuffController@index');

    $router->group(['prefix' => 'stuff'], function() use ($router) {
        // static routes : tetap
        $router->get('/data', 'StuffController@index');
        $router->post('/store', 'StuffController@store');
        $router->get('/trash', 'StuffController@trash');

        //dynamic routes : berubah - rubah
        $router->get('show/{id}', 'StuffController@show');
        $router->patch('update/{id}', 'StuffController@update');
        $router->delete('delete/{id}','StuffController@destroy');
        $router->get('/restore/{id}', 'StuffController@restore');
        $router->delete('/permanent/{id}', 'StuffController@forceDestroy');
    });

    // $router->post('/login', 'UserController@login');
    // $router->get('/logout', 'UserController@logout');

    $router->group(['prefix' => 'user'], function() use ($router) {
        // static routes : tetap
        $router->get('/data', 'UserController@index');
        $router->post('/store', 'UserController@store');
        $router->get('/trash', 'UserController@trash');

        //dunamic routes : berubah - rubah
        $router->get('{id}', 'UserController@show');
        $router->patch('/{id}', 'UserController@update');
        $router->delete('/{id}','UserController@destroy');
        $router->get('/restore/{id}', 'UserController@restore');
        $router->delete('/permanent/{id}', 'UserController@forceDestroy');
    }); 

    $router->group(['prefix' => 'inbound-stuff', 'middleware' => 'auth'], function() use ($router) {
        $router->get('/data', 'InboundStuffController@index');
        $router->post('/store', 'InboundStuffController@store');
        $router->get('/show/{id}', 'InboundStuffController@show');
        $router->patch('/update/{id}', 'InboundStuffController@update');
        $router->delete('/delete/{id}', 'InboundStuffController@destroy');
        $router->get('/trash', 'InboundStuffController@trash');
        $router->get('/restore/{id}', 'InboundStuffController@restore');
        $router->delete('/force-delete/{id}', 'InboundStuffController@forceDestroy');
    }); 

    $router->group(['prefix' => 'stuff-stock', 'middleware' => 'auth'], function() use ($router) {
        $router->get('/data', 'StuffStockController@index');
        $router->get('/detail/{id}', 'StuffStockController@show');
        $router->patch('/update/{id}', 'StuffStockController@update');
        $router->delete('/delete/{id}', 'StuffStockController@destroy');
        $router->get('/trash', 'StuffStockController@trash');
        $router->get('/restore/{id}', 'StuffStockController@restore');
        $router->get('/force-delete/{id}', 'StuffStockController@forceDestroy');
        $router->post('/add-stock/{id}', 'StuffStockController@addStock');
        $router->post('/sub-stock/{id}', 'StuffStockController@subStock');
    });

    $router->group(['prefix' => 'lending'], function() use ($router) {
        $router->get('/data', 'LendingController@index');
        $router->post('/store', 'LendingController@store');
        $router->get('/recycleBin', 'LendingController@recycleBin');
        $router->get('/show/{id}', 'LendingController@show');
        $router->patch('/update/{id}', 'LendingController@update');
        $router->delete('/delete/{id}', 'LendingController@destroy');
    });

    $router->group(['prefix' => 'restoration', 'middleware' => 'auth'], function() use ($router) {
        $router->post('/store', 'RestorationController@store');
    });

    // $router->get('/', function() use ($router) {
    //     return $router->app->version();
    // });
    
    $router->post('/login', 'AuthController@login');
    $router->get('/logout', 'AuthController@logout');
    $router->get('/profile', 'AuthController@me');
});
    