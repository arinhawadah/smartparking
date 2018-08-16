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
Route::get('reservation/expired/{id_user_park}', 'ReservationController@updateStatus'); //baru
Route::get('carparkslot','CarParkSlotController@index');
Route::get('carparkslot/{arrive_time}-{leaving_time}','CarParkSlotController@statusAvailableSlot');
Route::get('carparkslot/checkslot/{id_user_park}','CarParkSlotController@statusById'); //revisi
Route::get('history/{id_user}','HistoryController@historybyId');
Route::get('balance/penaltycharge/{id_user}','UserBalanceController@penaltyCharge');//ubah
Route::get('balance/{id_user}','UserBalanceController@show');
Route::patch('balance/addcharge/{id_user_park}','UserBalanceController@additionalCharge'); //revisi

// sensor
Route::get('parksensor/id_sensor={id_sensor}&&status={status}','ParkSensorResponseController@addSensor');
Route::get('parksensor/id_sensor={id_sensor}','ParkSensorController@getSensorStatus');
