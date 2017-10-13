<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

//$router->get('/', 'ExampleController@getTest');

$router->post('admin/login', 'Admin\LoginController@postLogin');
$router->group(['middleware' => 'admin'], function($router)
{
    $router->post('/admin/user', 'Admin\UsersController@store');
    $router->get('/admin/user/all', 'Admin\UsersController@getAll');
});

$router->get('/activation', 'Auth\ActivateAccountController@show');
$router->post('/resetpassword', 'Auth\ActivateAccountController@edit');

$router->post('/login', 'Auth\LoginController@postLogin');

$router->group(['middleware' => 'auth:api'], function($router)
{
    $router->post('/user/post', 'PostController@store');
    $router->put('/user/post/{id}/update', 'PostController@update');
    $router->delete('/user/post/{id}/delete', 'PostController@destroy');
    $router->get('/post/{id}/like', 'LikeController@update');
});