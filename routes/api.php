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
        Route::get('/test', 'Api\CharacterController@test');
    });
    
    // 後臺用路由
    Route::group(['as' => 'webadmin.'], function () {
        Route::get('/user', 'Backend\AuthenticationController@userInfo');
        Route::group(['middleware' => 'verify.backend'], function () {
            Route::patch('/user', 'Backend\AuthenticationController@editProfile');
        });
    });

    // 前端網頁用路由
    Route::group(['as' => 'webfront.'], function () {
        Route::get('/version', 'WebController@getVersionId');
        Route::get('/version/all', 'WebController@getAllVersions');
    });
});