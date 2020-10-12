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
    // 會驗證登入狀態的路由
    Route::group(['middleware' => 'verify.backend'], function () {
        Route::get('/', 'Backend\ViewController@index');
    });

    Route::get('/authentication', 'Backend\ViewController@login')->name('login');

    Route::post('/login', 'Backend\AuthenticationController@login');
    Route::get('/logout', 'Backend\AuthenticationController@logout');
});
