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
            Route::get('/', 'Backend\ViewController@index')->name('admin.index');
        });

        // 有權才可檢視的路由
        Route::group(['middleware' => 'verify.permission:view'], function () {
            // 審核
            Route::get('/verify', 'Backend\ViewController@verifyEditableApply');
            // 角色資料管理
            Route::get('/character', 'Backend\ViewController@characterList');
            // 角色關聯的資料管理
            Route::get('/character/related', 'Backend\ViewController@characterRelatedData');
        });
    });

    Route::get('/authentication', 'Backend\ViewController@login')->name('login');
});

// APIs
// 登入
Route::post('/admin/login', 'Backend\AuthenticationController@login');
// 註冊
Route::post('/admin/register', 'Backend\AuthenticationController@register');
// 登出
Route::get('/admin/logout', 'Backend\AuthenticationController@logout')->name('logout');
Route::group(['as' => 'webadmin.', 'prefix' => 'api'], function () {
    Route::group(['prefix' => 'v1'], function () {
        // 會驗登入的路由
        Route::group(['middleware' => 'verify.backend'], function () {
            // 取得使用者資料
            Route::get('/user', 'Backend\AuthenticationController@userInfo');
            // 編輯使用者資料
            Route::patch('/user', 'Backend\AuthenticationController@editProfile');

            // 會驗檢視權限的路由
            Route::group(['middleware' => 'verify.permission:view'], function () {
                // 取得待審核與已審核清單
                Route::get('/user/verify', 'Backend\SystemConfigController@getVerifyUsers');
            });

            // 會驗編輯權限的路由
            Route::group(['middleware' => 'verify.permission:edit'], function () {
                // 通過或拒絕審核
                Route::patch('/user/verify/verify', 'Backend\SystemConfigController@verifyUser');
                // 停權或復權帳號
                Route::patch('/user/verify/admin', 'Backend\SystemConfigController@adminAccount');
                // 新增角色資料
                Route::post('/character', 'Backend\CharacterController@addCharacter');
                // 編輯聲優、公會、種族、技能種類資料
                Route::post('/character/{data?}', 'Backend\CharacterController@addRelatedData');
                // 編輯聲優、公會、種族、技能種類資料
                Route::patch('/character/{data?}', 'Backend\CharacterController@editRelatedData');
            });
        });
    });
});
