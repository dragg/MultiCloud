<?php

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::get('/layout', function(){
   return view('layout');
});

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController'
]);

Route::group(['namespace' => 'Cloud'], function() {
    Route::get('/dropbox-auth-start', 'CloudAuthController@authDropbox');
    Route::get('/dropbox-auth-finish', 'CloudAuthController@callbackDropbox');

    Route::get('/yandex-auth-start', 'CloudAuthController@authYandex');
    Route::get('/yandex-auth-finish', 'CloudAuthController@callbackYandex');

    Route::get('/google-auth-start', 'CloudAuthController@authGoogle');
    Route::get('/google-auth-finish', 'CloudAuthController@callbackGoogle');
});

Route::resource('clouds', 'CloudController');

Route::resource('clouds.contents', 'ContentController');

Route::get('tasks', 'TaskController@index');
