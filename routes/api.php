<?php

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
    Route::post('issue', 'AuthenticationController@issue');
    Route::post('password/email', 'ForgotPasswordController@send');
    Route::post('password/reset', 'ResetPasswordController@reset')->name('password.reset');
});

Route::group(['prefix' => 'system', 'namespace' => 'System'], function () {
    Route::get('oauth/platform', 'PlatformController@status');
});

Route::group(['middleware' => 'auth:users,customer'], function () {
    Route::get('dashboard', 'DashboardController@index');
    Route::apiResource('trading', 'TradingController')->except('update');
    Route::apiResource('user', 'UserController');
    Route::apiResource('customer', 'CustomerController');
    Route::get('commodity/brand', 'CommodityController@brand');
    Route::get('commodity/category', 'CommodityController@category');
    Route::get('commodity/unit', 'CommodityController@unit');
    Route::post('commodity/image', 'CommodityController@image');
    Route::apiResource('commodity', 'CommodityController');
    Route::apiResource('discount', 'DiscountController');
    Route::put('order/{order}/status', 'OrderController@status');
    Route::apiResource('order', 'OrderController');
    Route::post('profile/avatar', 'ProfileController@avatar');
    Route::post('profile/password', 'ProfileController@password');
    Route::post('profile/address', 'ProfileController@address');
    Route::get('profile/{user}', 'ProfileController@show');
    Route::put('profile/{user}', 'ProfileController@update');
    Route::apiResource('invitation', 'InvitationController')->except('update');
    Route::put('logistics/{logistic}/number', 'LogisticsController@number');
    Route::apiResource('logistics', 'LogisticsController');
    Route::apiResource('journal', 'JournalController')->except(['store', 'update', 'destroy']);
});
