<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\FacebookController;
use App\Http\Controllers\AppController;

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

Route::middleware('json.response', 'auth:api')->get('/user', [ApiAuthController::class, 'get_user']);

//Public Routes
Route::group(['middleware' => ['json.response']], function () {
    //Route::post('/whatever', 'WhateverController@method')->name('route_name');

    Route::post('/login', [ApiAuthController::class, 'login'])->name('api.login');
    Route::post('/create_user', [ApiAuthController::class, 'register_user'])->name('api.create_user_provisional');
});

//Private Routes
Route::middleware('json.response', 'auth:api')->group(function () {
    //Route::post('/whatever', 'WhateverController@method')->middleware('api.admin/api.superadmin')->name('route_name');

    Route::post('/logout', [ApiAuthController::class, 'logout'])->name('api.logout');

    //Users
    Route::group(['middleware' => 'api.superAdmin'], function () {
        Route::resource('users', UserController::class);
    });

    Route::get('/users_filter', [UserController::class, 'usersFilter'])->name('api.users_filter');        

    //Empresas
    Route::group(['prefix' => 'empresas'], function () {
        Route::get('/', [EmpresaController::class, 'index'])->middleware('api.admin')->name('api.empresas_list');
        Route::post('/', [EmpresaController::class, 'store'])->middleware('api.superAdmin')->name('api.empresas_store');
        Route::get('/{empresa}', [EmpresaController::class, 'show'])->name('api.empresas_info');
        //Route::put('/{empresa}', [EmpresaController::class, 'update'])->middleware('api.superAdmin')->name('api.empresas_update');
        Route::post('/update/{empresa}', [EmpresaController::class, 'update'])->middleware('api.superAdmin')->name('api.empresas_update');
        Route::delete('/{empresa}', [EmpresaController::class, 'destroy'])->middleware('api.superAdmin')->name('api.empresas_delete');
        //Route::post('/readFbData', [EmpresaController::class, 'readFbData'])->name('api.empresas_readfb');
    });

    //Facebook
    Route::group(['prefix' => 'facebook'], function () {
        Route::get('/published_posts', [FacebookController::class, 'getPublishedPosts'])->middleware('api.admin')->name('api.facebook_published_posts_get');
        Route::get('/page_insights', [FacebookController::class, 'getPageInsights'])->middleware('api.admin')->name('api.facebook_page_insights_get');
        Route::get('/emp_adaccounts', [FacebookController::class, 'getCompanyAdAccounts'])->middleware('api.admin')->name('api.facebook_ad_accounts_get');
        Route::get('/emp_campaigns', [FacebookController::class, 'getCompanyCampaigns'])->middleware('api.admin')->name('api.facebook_campaigns_get');
        Route::get('/emp_ads', [FacebookController::class, 'getCompanyAds'])->middleware('api.admin')->name('api.facebook_ads_get');
        Route::post('/readFbData', [FacebookController::class, 'readFbData'])->name('api.facebook_readfb');
        Route::get('/export_published_posts/{empresa}', [FacebookController::class, 'exportPublishedPosts'])->middleware('api.admin')->name('api.facebook_published_posts_export');
        Route::get('/export_page_insights/{empresa}', [FacebookController::class, 'exportPageInsights'])->middleware('api.admin')->name('api.facebook_page_insights_export');
    });

    //Apps
    Route::group(['prefix' => 'apps'], function () {
        Route::get('/', [AppController::class, 'index'])->middleware('api.admin')->name('api.apps_list');
    });
});

/*Route::middleware('auth:api')->group(function () {
    Route::group(['prefix' => 'facebook'], function () {
        Route::post('/readFbData', [FacebookController::class, 'readFbData'])->name('api.empresas_readfb');
    });
});*/
