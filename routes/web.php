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
// Route::get('/home', 'HomeController@index')->name('home');

Route::resource('sensor-admin', 'ParkSensorController');
// Route::get('admin/sensor','ParkSensorController@status');
// Route::get('admin/sensor/{entry}/edit','ParkSensorController@sensorbyId')->name('search-sensor');
// Route::post('admin/updatesensor/{entry}','ParkSensorController@updateSensor')->name('update-sensor');
// Route::post('admin/addsensor','ParkSensorController@addSensor')->name('create-sensor');
// Route::delete('admin/deletesensor/{id_sensor}','ParkSensorController@deleteParkSensor')->name('delete-sensor');

Route::resource('slot-admin', 'CarParkSlotController');
// Route::get('admin/carparkslot','CarParkSlotController@status');
// Route::get('admin/carparkslot/{id_slot}/edit','CarParkSlotController@slotbyId')->name('search-slot');
// Route::post('admin/updateslot/{slot_name}', 'CarParkSlotController@updateParkSlot')->name('update-slot');
// Route::post('admin/addslot', 'CarParkSlotController@createParkSlot')->name('create-slot');
// Route::delete('admin/deleteslot/{slot_name}','CarParkSlotController@deleteParkSlot')->name('delete-slot');
Route::get('admin/carparkslot/{time}','CarParkSlotController@slotByTime');

Route::resource('user-admin', 'UserController');
Route::get('user-admin/profile','UserController@profile');
// Route::get('admin/showuser','UserController@users');
Route::get('admin/createuser','UserController@createUser');
Route::post('user-admin/search','UserController@showUserbyEmail')->name('user-search');
// Route::get('admin/showuserid/{id_user}/edit','UserController@showUserbyId')->name('search-user');
// Route::delete('admin/deleteuser/{id_user}','UserController@deleteUser')->name('delete-user');
// Route::post('users/updateprofile/{id_user}','UserController@updateProfile')->name('update-user');

Route::resource('reservation-admin', 'ReservationController');
Route::post('reservation-admin/search','ReservationController@showReservationbyUser')->name('reservation-search');
Route::get('reservations','DashboardController@allSlotByTime')->name('allreservations');
// Route::get('admin/showreservation','ReservationController@reservation');
// Route::get('admin/showreservationid/{id_user_park}/edit','ReservationController@showReservationbyId')->name('search-reservation');
// Route::post('admin/updatereserevation/{id_user_park}','ReservationController@updateAdminReservation')->name('update-reservation');
// Route::post('admin/addreservation','ReservationController@addAdminReservation')->name('create-reservation');
// Route::delete('admin/deletereserevation/{id_user_park}','ReservationController@deleteReservation')->name('delete-reservation');

Route::post('auth/register-admin', 'AuthController@registerAdmin')->name('register-admin');
// Route::post('auth/register-user','AuthController@register')->name('register-user');
// Route::post('auth/user/resetpassword','AuthController@ResetPassword')->name('reset-password');

Route::resource('balance-admin', 'UserBalanceController');
Route::post('balance-admin/search', 'UserBalanceController@search')->name('balance-search');