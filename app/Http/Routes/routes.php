<?php

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController'
]);

Route::group(['middleware' => ['auth']], function() {

    Route::get('/', 'HomeController@index');

    Route::resource('clouds', 'CloudController');

    Route::resource('clouds.contents', 'ContentController');

    Route::get('tasks', 'TaskController@index');

    //Need leave it at the end of file! Or need change all auth url on clouds!
    Route::controller('/', 'Cloud\CloudAuthController');

});
