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

Route::get('/', function() {
    return view('index');
});

Route::get('/mylist', 'AccountController@mylist');
Route::get('/mylocation', 'AccountController@mylocation');
Route::get('/profile', 'AccountController@profile');
Route::get('/message', 'AccountController@message');

Route::resource('/list', 'ListController');

Route::get('/login', 'Auth\AuthController@getLogin');
Route::post('/login', 'Auth\AuthController@postLogin');
Route::get('/signup', 'Auth\AuthController@getSignup');
Route::post('/signup', 'Auth\AuthController@postSignup');
Route::get('/logout', 'Auth\AuthController@getLogout');

Route::get('/password/forgot', 'Auth\PasswordController@getForgotPassword');
Route::post('/password/forgot', 'Auth\PasswordController@postForgotPassword');
Route::get('/password/reset', 'Auth\PasswordController@getResetPassword');
Route::post('/password/reset', 'Auth\PasswordController@postResetPassword');

Route::get('/email/verify', 'Auth\EmailController@verify');
Route::get('/email/resend', 'Auth\EmailController@resend');
//Route::controller('/', 'Auth\AuthController');

//Route::controllers([
//	'auth' => 'Auth\AuthController',
//	'password' => 'Auth\PasswordController',
//]);
