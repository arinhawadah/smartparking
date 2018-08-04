<?php

use Illuminate\Http\Request;

// /*
// |--------------------------------------------------------------------------
// | API Routes
// |--------------------------------------------------------------------------
// |
// | Here is where you can register API routes for your application. These
// | routes are loaded by the RouteServiceProvider within a group which
// | is assigned the "api" middleware group. Enjoy building your API!
// |
// */

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Auth::routes();
//user
Route::post('auth/register-user','AuthController@register')->name('register-user');
Route::post('auth/login','AuthController@login');
Route::post('auth/resetpassword/sendemail','AuthController@ResetPassword')->name('reset-password');
// Route::get('users/profile','UserController@profile')->middleware('cors');
// Route::put('users/updateprofile/{id_user}','UserController@updateProfile');
Route::post('reservation/add','ReservationController@addReservation')->middleware('cors');
Route::delete('reservation/cancel/{id_user_park}','ReservationController@destroy');
Route::patch('reservation/changeslot/{id_user_park}','ReservationController@updateReservation'); //revisi
Route::get('carparkslot','CarParkSlotController@index');
Route::get('carparkslot/{arrive_time}-{leaving_time}','CarParkSlotController@statusAvailableSlot');
Route::get('carparkslot/checkslot/{id_user_park}','CarParkSlotController@statusById'); //revisi
Route::get('history/{id_user}','HistoryController@historybyId');
Route::get('balance/penaltycharge/{id_user}','UserBalanceController@penaltyCharge');//ubah
Route::get('balance/{id_user}','UserBalanceController@show');
Route::get('balance/addcharge/{id_user}','UserBalanceController@additionalCharge'); //revisi

//admin

// Route::post('auth/admin/login','AuthController@login'); // ke routes web
// Route::post('auth/admin/register', 'AuthController@registerAdmin')->middleware('cors');
// Route::get('admin/profile','UserController@profile');
// Route::put('admin/updatereserevation/{id_user_park}','ReservationController@updateReservation')->middleware('auth.basic');
// Route::delete('admin/deletereserevation/{id_user_park}','ReservationController@deleteReservation');
// Route::post('admin/addslot', 'CarParkSlotController@createParkSlot')->middleware('cors');
// Route::put('admin/updateslot/{slot_name}', 'CarParkSlotController@updateParkSlot')->middleware('cors');
// Route::delete('admin/deleteslot/{slot_name}','CarParkSlotController@deleteParkSlot')->middleware('cors');
// Route::delete('admin/deleteuser/{id_user}','UserController@deleteUser')->middleware('auth.basic');
// Route::get('admin/showuser','UserController@users')->middleware('auth.basic');
// Route::get('admin/showuserid/{id_user}','UserController@showUserbyId')->middleware('auth.basic');
// Route::get('admin/showuseremail/{email}','UserController@showUserbyEmail')->middleware('auth.basic');
// Route::get('admin/carparkslot/{time}','CarParkSlotController@statusByTime');
// Route::delete('deletesensor/{id_sensor}','ParkSensorController@deleteParkSensor')->middleware('cors');

// Route::resource('sensor-admin', 'ParkSensorController');

// Route::resource('slot-admin', 'CarParkSlotController');
// Route::get('admin/carparkslot/{time}','CarParkSlotController@slotByTime');

// Route::resource('user-admin', 'UserController');
// Route::get('profile','UserController@profile');
// Route::get('admin/createuser','UserController@createUser');
// Route::post('user-admin/search','UserController@showUserbyEmail')->name('user-search');

// Route::resource('reservation-admin', 'ReservationController')->middleware('auth.basic');
// Route::post('reservation-admin/search','ReservationController@showReservationbyUser')->name('reservation-search')->middleware('auth.basic');
// Route::get('reservations','DashboardController@allSlotByTime')->name('allreservations');

// Route::post('auth/register-admin', 'AuthController@registerAdmin')->name('register-admin');

// Route::resource('balance-admin', 'UserBalanceController');
// Route::post('balance-admin/search', 'UserBalanceController@search')->name('balance-search');

// sensor
Route::get('parksensor/id_sensor={id_sensor}&&status={status}','ParkSensorResponseController@addSensor');
Route::get('parksensor/id_sensor={id_sensor}','ParkSensorController@getSensorStatus');
