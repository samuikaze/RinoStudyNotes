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

Route::group(['prefix' => 'v1'], function () {
    // 公共 API
    Route::group(['as' => 'publicapi'], function () {
        Route::get('/characters', 'Api\CharacterController@characterList');
        Route::get('/character', 'Api\CharacterController@characterInfo');
        Route::get('/guilds', 'Api\CharacterController@guildList');
        Route::get('/skill/types', 'Api\CharacterController@skillTypeList');
        Route::get('/cvs', 'Api\CharacterController@CVList');
        Route::get('/races', 'Api\CharacterController@raceList');
    });
});
