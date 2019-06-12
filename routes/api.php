<?php

use Illuminate\Http\Request;

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



Route::group(['middleware' => 'auth:api-jwt'], function () {

    Route::any('ping', function() {
        return 'pong';
    });

});


// Handle JWT Authentication
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'Api\AuthController@login');

    Route::group(['middleware' => 'auth:api-jwt'], function () {
        Route::post('logout', 'Api\AuthController@logout');
        Route::post('refresh', 'Api\AuthController@refresh');
        Route::post('me', 'Api\AuthController@me');
    });
});

// Protected API routes:
Route::group(['middleware' => 'auth:api-jwt'], function () {
    // Put here all routes that require JWT authentication

    Route::get('ping', function(){
        return 'pong';
    });

});