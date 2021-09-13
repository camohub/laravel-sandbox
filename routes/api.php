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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/lurity-gateway', 'LurityPaymentGatewayController@index')->name('gateway');
Route::post('/lurity-checkout', 'LurityPaymentGatewayController@checkout')->name('gateway-checkout');
Route::get('/lurity-transaction/{id}', 'LurityPaymentGatewayController@transaction')->name('gateway-transaction');
Route::get('/lurity-gateway-client-token', 'LurityPaymentGatewayController@clientToken')->name('gateway-client-token');

Route::get('/lurity-orsr/{ico}', 'LurityORSRController@index')->name('orsr-api');