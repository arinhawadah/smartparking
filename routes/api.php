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

//user
Route::post('auth/user/register','AuthController@register');
Route::post('auth/login','AuthController@login');
Route::post('auth/user/resetpassword','AuthController@ResetPassword'); //reset password
Route::get('users/profile','UserController@profile')->middleware('cors');
Route::put('users/updateprofile/{id_user}','UserController@updateProfile');
Route::post('addreservation','ReservationController@addReservation')->middleware('cors');
Route::delete('user/deletereserevation/{id_user_park}','ReservationController@deleteReservation');
Route::get('carparkslot','CarParkSlotController@status');
Route::get('carparkslot/{arrive_time}-{leaving_time}','CarParkSlotController@statusAvailableSlot');
Route::get('carparkslot/{id_user_park}','CarParkSlotController@statusById');
Route::get('history/{id_user}','HistoryController@historybyId');

// Route::get('cobacoba','CarParkSlotController@cobacoba');

//admin

// Route::post('auth/admin/login','AuthController@login'); // ke routes web
// Route::post('auth/admin/register', 'AuthController@registerAdmin')->middleware('cors');
// Route::get('admin/profile','UserController@profile');
// Route::put('admin/updatereserevation/{id_user_park}','ReservationController@updateReservation')->middleware('cors');
// Route::delete('admin/deletereserevation/{id_user_park}','ReservationController@deleteReservation');
Route::post('admin/addslot', 'CarParkSlotController@createParkSlot')->middleware('cors');
// Route::put('admin/updateslot/{slot_name}', 'CarParkSlotController@updateParkSlot')->middleware('cors');
Route::delete('admin/deleteslot/{slot_name}','CarParkSlotController@deleteParkSlot')->middleware('cors');
// Route::delete('admin/deleteuser/{id_user}','UserController@deleteUser')->middleware('auth.basic');
// Route::get('admin/showuser','UserController@users')->middleware('auth.basic');
// Route::get('admin/showuserid/{id_user}','UserController@showUserbyId')->middleware('auth.basic');
// Route::get('admin/showuseremail/{email}','UserController@showUserbyEmail')->middleware('auth.basic');
Route::get('admin/carparkslot/{time}','CarParkSlotController@statusByTime');
Route::delete('deletesensor/{id_sensor}','ParkSensorController@deleteParkSensor')->middleware('cors');

// sensor
Route::get('parksensor/id_sensor={id_sensor}&&status={status}','ParkSensorResponseController@addSensor');
Route::get('parksensor/id_sensor={id_sensor}','ParkSensorController@getSensorStatus');
