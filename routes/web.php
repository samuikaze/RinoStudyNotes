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

Route::group(['namespace' => 'v1'], function () {
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
                // 角色資料管理
                Route::get('/character', 'Backend\ViewController@characterList');
                // 角色關聯的資料管理
                Route::get('/character/related', 'Backend\ViewController@characterRelatedData');
                // 角色專用武器管理
                Route::get('/character/specialweapon', 'Backend\ViewController@characterSpecialWeapon');
            });

            // 僅有管理員可以存取的路由
            Route::group(['middleware' => 'verify.permission:admin'], function () {
                // 審核
                Route::get('/verify', 'Backend\ViewController@verifyEditableApply');
                // 版本管理
                Route::get('/versions', 'Backend\ViewController@versionControl');
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
    Route::get('/frontend/version', 'WebController@getVersionId');
    Route::get('/frontend/version/all', 'WebController@getAllVersions');
    Route::group(['as' => 'webadmin.', 'prefix' => 'webapi'], function () {
        // 會驗登入的路由
        Route::group(['middleware' => 'verify.backend'], function () {
            // 取得使用者資料
            Route::get('/user', 'Backend\AuthenticationController@userInfo');
            // 編輯使用者資料
            Route::patch('/user', 'Backend\AuthenticationController@editProfile');

            // 會驗檢視權限的路由
            Route::group(['middleware' => 'verify.permission:view'], function () {
                // 取得待審核與已審核清單
                Route::get('/user/verify', 'Backend\SystemVarController@getVerifyUsers');
                // [後台] 取得角色資料
                Route::get('/admin/api/character/{id?}', 'Backend\CharacterController@characterInfo');
                Route::get('/characters', 'Api\CharacterController@characterList');
                Route::get('/character', 'Api\CharacterController@characterInfo');
                Route::get('/guilds', 'Api\CharacterController@guildList');
                Route::get('/skill/types', 'Api\CharacterController@skillTypeList');
                Route::get('/cvs', 'Api\CharacterController@CVList');
                Route::get('/races', 'Api\CharacterController@raceList');
                Route::get('/specialweapons', 'Api\CharacterController@specialWeaponList');
                Route::get('/specialweapon', 'Backend\CharacterController@getSpecialWeaponInfo');
            });

            // 會驗編輯權限的路由
            Route::group(['middleware' => 'verify.permission:edit'], function () {
                // 新增角色資料
                Route::post('/character', 'Backend\CharacterController@addCharacter');
                // 編輯角色資料
                Route::patch('/character', 'Backend\CharacterController@editCharacter');
                // 編輯聲優、公會、種族、技能種類資料
                Route::post('/character/{data?}', 'Backend\CharacterController@addRelatedData');
                // 編輯聲優、公會、種族、技能種類資料
                Route::patch('/character/{data?}', 'Backend\CharacterController@editRelatedData');
                // 新增專用武器資料
                Route::post('/specialweapon', 'Backend\CharacterController@addSpecialWeapon');
                // 編輯專用武器資料
                Route::patch('/specialweapon', 'Backend\CharacterController@editSpecialWeapon');
            });

            // 僅有管理員可存取的路由
            Route::group(['middleware' => 'verify.permission:admin'], function () {
                // 通過或拒絕審核
                Route::patch('/user/verify/verify', 'Backend\SystemVarController@verifyUser');
                // 停權或復權帳號
                Route::patch('/user/verify/admin', 'Backend\SystemVarController@adminAccount');
                // 新增版本
                Route::post('/version', 'Backend\SystemVarController@addVersion');
                // 修改版本
                Route::patch('/version', 'Backend\SystemVarController@editVersion');
                // 刪除版本
                Route::delete('/version', 'Backend\SystemVarController@deleteVersion');
            });
        });
    });
});
