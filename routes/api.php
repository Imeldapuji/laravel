<?php

use Illuminate\Http\Request;

Route::post('register', 'UserController@register');
Route::post('login', 'UserController@login');

Route::middleware(['jwt.verify'])->group(function(){
	Route::get('saldo', 'SaldoController@saldo');
	Route::get('saldoall', 'SaldoController@saldoAuth');
    Route::get('user', 'UserController@getAuthenticatedUser');
	Route::post('update', 'UserController@getAuthenticatedUser');
	Route::get('saldo', 'SaldoController@index');
    Route::post('saldo', 'SaldoController@create');
    //Route::get('/saldo/{id}', 'SaldoController@detail');
    Route::put('/saldo/{id}', 'SaldoController@update');
    
});

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});