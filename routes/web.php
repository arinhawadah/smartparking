<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('auth/activate', 'Auth\ActivationController@activate')->name('auth.activate');
Route::get('auth/resetpassword', 'Auth\ResetPasswordController@resetpassword')->name('auth.resetpassword');
Route::get('/home', 'HomeController@index')->name('home');
