<?php

/*
|--------------------------------------------------------------------------
| Swagger API Document Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Swagger API documents routes for your application. These
| routes are loaded by the RouteServiceProvider. Enjoy building your API documentation!
|
*/

Route::namespace('Swagger')->group(function () {
    Route::get('swagger/{version}', 'SwaggerController@index');
});
