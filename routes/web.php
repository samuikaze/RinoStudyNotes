<?php

use Illuminate\Support\Facades\Route;

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

// 前台介面
Route::get('/', 'FrontendController@index');

// API 一覽
Route::get('/api/all', 'FrontendController@apiList');

// 版本紀錄
Route::get('/version/history', 'FrontendController@versionList');

// 管理介面
Route::group(['prefix' => 'admin'], function () {
    // 會驗證登入狀態路由
    Route::group(['middleware' => 'verify.backend'], function () {
        // 公共路由，不會驗證存取權限
        Route::group(['middleware' => 'verify.permission:public'], function () {
            Route::get('/', 'Backend\ViewController@index');
        });

        // 有權才可檢視的路由
        Route::group(['middleware' => 'verify.permission:view'], function () {
            Route::get('/verify', 'Backend\ViewController@verifyEditableApply');
        });
    });

    Route::get('/authentication', 'Backend\ViewController@login')->name('login');

    Route::post('/login', 'Backend\AuthenticationController@login');
    Route::post('/register', 'Backend\AuthenticationController@register');
    Route::get('/logout', 'Backend\AuthenticationController@logout');
});
