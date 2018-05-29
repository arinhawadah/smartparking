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
    return view('dashboard');
})->middleware('auth');

Auth::routes();

Route::get('auth/activate', 'Auth\ActivationController@activate')->name('auth.activate');
Route::get('auth/resetpassword', 'Auth\ResetPasswordController@resetpassword')->name('auth.resetpassword');
Route::get('/home', 'HomeController@index')->name('home');

Route::get('admin/carparkslot','CarParkSlotController@status');
Route::get('admin/carparkslot/{id_slot}/edit','CarParkSlotController@slotbyId')->name('search-slot');
Route::post('admin/updateslot/{slot_name}', 'CarParkSlotController@updateParkSlot')->name('update-slot');

Route::resource('user-admin', 'UserController');
Route::get('admin/profile','UserController@profile');
Route::get('admin/showuser','UserController@users');
Route::match(['GET', 'POST'],'admin/showuseremail','UserController@showUserbyEmail')->name('email-search');
Route::get('admin/showuserid/{id_user}/edit','UserController@showUserbyId')->name('search-user');
Route::delete('admin/deleteuser/{id_user}','UserController@deleteUser')->name('delete-user');
Route::post('users/updateprofile/{id_user}','UserController@updateProfile')->name('update-user');

Route::resource('reservation-admin', 'ReservationController');
Route::get('admin/showreservation','ReservationController@reservation');
Route::match(['GET', 'POST'], 'admin/showname','ReservationController@showReservationbyUser')->name('reservation-search');
Route::get('admin/showreservationid/{id_user_park}/edit','ReservationController@showReservationbyId')->name('search-reservation');
Route::post('admin/updatereserevation/{id_user_park}','ReservationController@updateReservation')->name('update-reservation');
Route::post('admin/addreservation','ReservationController@addAdminReservation')->name('reservation-admin');
Route::delete('admin/deletereserevation/{id_user_park}','ReservationController@deleteReservation')->name('delete-reservation');

Route::post('auth/admin/register', 'AuthController@registerAdmin')->name('register-admin');
