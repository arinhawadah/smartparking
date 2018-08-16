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
    return redirect('reservations');
})->middleware('auth');

Route::get('/calendar', function () {
    return view('calendar');
});

Auth::routes();

Route::get('auth/activate', 'Auth\ActivationController@activate')->name('auth.activate');
Route::get('auth/resetpassword', 'Auth\ResetPasswordController@resetpassword')->name('auth.resetpassword');

Route::resource('sensor-admin', 'ParkSensorController');

Route::resource('slot-admin', 'CarParkSlotController');

Route::resource('user-admin', 'UserController');
Route::get('profile','UserController@profile');
Route::get('admin/createuser','UserController@createUser');
Route::post('user-admin/search','UserController@showUserbyEmail')->name('user-search');
Route::get('user-admin/editpassword/{id_user}','UserController@editPassword');
Route::patch('user-admin/updatepassword/{id_user}', 'UserController@updatePassword')->name('admin-editpassword');

Route::resource('reservation-admin', 'ReservationController');
Route::post('reservation-admin/search','ReservationController@showReservationbyUser')->name('reservation-search');
Route::get('reservations','DashboardController@allSlotByTime')->name('allreservations');

Route::post('auth/register-admin', 'AuthController@registerAdmin')->name('register-admin');

Route::resource('balance-admin', 'UserBalanceController');
Route::post('balance-admin/search', 'UserBalanceController@search')->name('balance-search');