<?php

//use Illuminate\Http\Request;

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

Route::get('/user/data', 'UserController@getData')->middleware('jwt.auth:api');

Route::post('/auth/authenticate', 'Auth\LoginController@authenticate');


// TODO add restriction AMDin
Route::get('/admin/stats', 'Admin\UserController@getStats');; //->middleware('jwt.auth:api');
