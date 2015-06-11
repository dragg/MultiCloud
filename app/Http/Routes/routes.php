<?php

//Route::post('/auth/login', 'Auth\AuthenticateController@authenticate');

Route::controllers([
    'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController'
]);



Route::group(['middleware' => ['auth']], function() {

    Route::get('/', 'HomeController@index');

    Route::resource('clouds', 'CloudController', ['except' => 'create', 'edit']);

    Route::resource('clouds.contents', 'ContentController', ['except' => 'create', 'edit']);

    Route::resource('tasks', 'TaskController@index', ['only' => 'index']);

    //Need leave it at the end of file! Or need change all auth url on clouds!
    Route::controller('', 'Cloud\CloudAuthController');

});
