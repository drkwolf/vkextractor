<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/


//Route::get('user', 'UserController@getUser')->middleware('auth');

Route::get('/test2', function() {
    $user = \App\Models\User::all()->first();
    $data = new \App\Models\Data();
    $api = new \App\VK\ApiStandalone($user);
    //$api->getAllMsgs();
    //$response = $api->messages->getIncoming(['count' => 200]);
    //$friends = $api->friends->get();

    $wall = $api->wall->getAllWall(['owner_id' => 2, 'count' => 2]);

    $recent  = $api->friends->getRecent(['user_id' => 2]);
    dd($recent);
    //$response = $api->messages->getOutgoing();

    $response = $api->messages->getAllHistories() ;
    dump($response);
});

//Auth::routes();
//Route::get('/home', 'HomeController@index');


Route::get('/', function(){ return view('index'); });
Route::get('/{any}', function(){ return view('index'); });
