<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/create', 'MonitoringController@create');
Route::get('/show/latest', 'MonitoringController@showData');
Route::get('/show/all', 'MonitoringController@showAll');
Route::get('/show/month', 'MonitoringController@showCurrentData');