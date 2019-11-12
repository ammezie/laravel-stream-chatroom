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

Route::post('/generate-token', 'ChatController@generateToken');
Route::post('/leave-channel', 'ChatController@leaveChannel');
Route::post('/join-channel', 'ChatController@joinChannel')->name('join-channel');
