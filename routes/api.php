<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\PaymentController;
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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/


Route::controller(ClientController::class)->group(function () {
    Route::get('clients',  'index')->name('clients');
    Route::get('clients/{client}',  'show')->name('clients.show');

});

Route::controller(PaymentController::class)->group(function () {
    Route::get('payments',  'index')->name('payments');
    Route::post('payments',  'store')->name('payments.store');

});
