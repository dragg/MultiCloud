<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::get('/layout', function(){
   return view('layout');
});

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::group(['namespace' => 'Cloud'], function() {
    Route::get('/dropbox-auth-start', 'CloudAuthController@authDropbox');

    Route::get('/dropbox-auth-finish', 'CloudAuthController@callbackDropbox');

    Route::get('/dropbox-profile/{id}', 'DropboxController@show');
    Route::get('/dropbox-exit/{id}', 'DropboxController@destroy');


    Route::get('/yandex-auth-start', 'CloudAuthController@authYandex');

    Route::get('/yandex-auth-finish', 'CloudAuthController@callbackYandex');

    Route::get('/yandex-profile/{id}', 'YandexController@show');
    Route::get('/yandex-exit/{id}', 'YandexController@destroy');

    Route::get('/google-auth-start', 'CloudAuthController@authGoogle');

    Route::get('/google-auth-finish', 'CloudAuthController@callbackGoogle');

    Route::get('/google-profile/{id}', 'GoogleController@show');
    Route::get('/google-exit/{id}', 'GoogleController@destroy');
});

Route::resource('clouds', 'CloudsController');
