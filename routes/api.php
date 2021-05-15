<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\FacebookController;

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

    //Empresas
    Route::group(['prefix' => 'empresas'], function () {
        Route::get('/', [EmpresaController::class, 'index'])->middleware('api.admin')->name('api.empresas_list');
        Route::post('/', [EmpresaController::class, 'store'])->middleware('api.superAdmin')->name('api.empresas_store');
        Route::get('/{empresa}', [EmpresaController::class, 'show'])->name('api.empresas_info');
        Route::put('/{empresa}', [EmpresaController::class, 'update'])->middleware('api.superAdmin')->name('api.empresas_update');
        Route::delete('/{empresa}', [EmpresaController::class, 'destroy'])->middleware('api.superAdmin')->name('api.empresas_delete');
        Route::post('/readFbData', [EmpresaController::class, 'readFbData'])->name('api.empresas_readfb');
    });

    //Facebook
    Route::group(['prefix' => 'facebook'], function () {
        Route::get('/published_posts', [FacebookController::class, 'getPublishedPosts'])->middleware('api.admin')->name('api.facebook_published_posts_get');
    });
});

Route::middleware('auth:api')->group(function () {
    Route::group(['prefix' => 'empresas'], function () {
        Route::post('/readFbData', [EmpresaController::class, 'readFbData'])->name('api.empresas_readfb');
    });
});
