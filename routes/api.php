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
Route::get('users/profile','UserController@profile')->middleware('auth.basic');
Route::put('users/updateprofile/{id_user}','UserController@updateProfile');
Route::get('users','UserController@users')->middleware('auth:api');
Route::post('addreservation','ReservationController@addReservation')->middleware('auth:api');
Route::get('carparkslot','CarParkSlotController@status');

//admin
Route::post('auth/admin/register', 'AuthController@registerAdmin');
Route::put('admin/updatereserevation/{id_reservation}','ReservationController@updateReservation')->middleware('auth.basic');
Route::delete('admin/deletereserevation/{id_reservation}','ReservationController@deleteReservation')->middleware('auth.basic');
Route::post('admin/addslot', 'CarParkSlotController@createParkSlot')->middleware('auth.basic');
Route::put('admin/updateslot/{slot_name}', 'CarParkSlotController@updateParkSlot')->middleware('auth.basic');
Route::delete('admin/deleteslot/{slot_name}','CarParkSlotController@deleteParkSlot')->middleware('auth.basic');
Route::delete('admin/deleteuser/{id_user}','UserController@deleteUser')->middleware('auth.basic');
Route::get('admin/showuserid/{id_user}','UserController@showUserbyId')->middleware('auth.basic');
Route::get('admin/showuseremail/{email}','UserController@showUserbyEmail')->middleware('auth.basic');
Route::get('carparkslot/{arrive_time}-{leaving_time}','CarParkSlotController@statusByTime');

// sensor
Route::post('parksensor/add','ParkSensorController@addSensor');
