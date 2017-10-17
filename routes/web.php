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

$router->group(['prefix' => 'api/v1'], function ($router){

// admin login
$router->post('admin/login', 'Admin\LoginController@login');
// group for loggined admin
$router->group(['middleware' => 'admin', 'prefix' => 'admin'], function($router)
{
    $router->post('users', 'Admin\UsersController@store');
    $router->get('users/all', 'Admin\UsersController@readAll');
});

// login and account activation
$router->group(['namespace' => 'Auth'], function($router){
	$router->get('/activation', ['as' => 'activation', 'uses' => 'ActivateAccountController@show']);
	$router->post('/resetpassword', ['as' => 'resetpassword', 'uses'=> 'ActivateAccountController@updatePassword']);
	$router->post('/login', 'LoginController@login');
});


// routes for loggined users
$router->group(['middleware' => 'auth:api'], function($router)
{
	// routes with operations for posts
	$router->group(['prefix' => 'posts'], function($router){
		$router->post('/', 'PostController@store');
		$router->put('{id}', 'PostController@update');
    	$router->delete('{id}', 'PostController@destroy');
    	$router->post('{id}/like', 'LikeController@store');
    	$router->get('most/rated', 'PostController@readMostRated');
    	$router->get('random/rated', 'PostController@readRandomRated');
	});

	$router->group(['prefix' => 'user'], function($router){
		$router->get('{id}', 'UserController@read');
	});
});

});