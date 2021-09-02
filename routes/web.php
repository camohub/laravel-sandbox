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

Route::get('/', 'DefaultController@index')->name('index');
Route::get('/lurity-gateway', 'LurityPaymentGatewayController@index')->name('gateway');
Route::get('/lurity-gatewayNonce', 'LurityPaymentGatewayController@clientToken')->name('gateway-nonce');
Route::get('/lurity-gateway-client-token', 'LurityPaymentGatewayController@clientToken')->name('gateway-client-token');
