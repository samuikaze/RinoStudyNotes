<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::group(['namespace' => 'v1'], function () {
    Route::group(['prefix' => 'v1'], function () {
        // 公共 API
        Route::group(['as' => 'publicapi'], function () {
            Route::get('/check/uptime', 'Api\SystemController@uptimeCheck');
            Route::get('/character', 'Api\CharacterController@characterInfo');
            Route::get('/character/{search?}', 'Api\CharacterController@characterInfo');
            Route::get('/specialweapons', 'Api\CharacterController@specialWeaponList');
        });
    });
});
