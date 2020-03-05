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

Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function () {
    Route::post('signin', 'AuthenticationController@signin')->name('auth.signin');
    Route::post('applet/session', 'AuthenticationController@session')->name('auth.session');
    Route::get('profile', 'AuthenticationController@profile')->name('auth.profile');
    Route::post('signout', 'AuthenticationController@signout')->name('auth.signout');
    Route::post('register', 'RegisterController@register')->name('auth.register');
    Route::get('email/verify/{id}', 'VerificationController@verify')->name('verification.verify');
    Route::post('email/resend', 'VerificationController@resend')->name('verification.resend');
});

Route::group(['middleware' => 'auth:users'], function () {
    Route::get('dashboard', 'DashboardController@index');
    Route::apiResource('trading', 'TradingController')->except('update');
    Route::apiResource('customer', 'CustomerController');
    Route::get('commodity/brand', 'CommodityController@brand');
    Route::get('commodity/category', 'CommodityController@category');
    Route::apiResource('commodity', 'CommodityController');
    Route::apiResource('order', 'OrderController');
});

//Route::middleware('auth:users,customer')->get('/user', function (Request $request) {
//    return $request->user();
//});
