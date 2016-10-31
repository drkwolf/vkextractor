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

Route::get('/', function(){
  return view('welcome');
});

Route::get('login/', 'Auth\LoginController@redirectToProvider');
Route::get('login/callback', 'Auth\LoginController@handleProviderCallback');

//user home
//Route::get('user', 'VkUserController@index');
//Route::get('user/data', 'VkUserController@index');

Route::get('/test2', function() {
    $user = \App\Models\User::all()->first();
    $data = new \App\Models\Data();
    $api = new \App\VK\ApiStandalone($user);
    //$api->getAllMsgs();
    //$response = $api->messages->getIncoming(['count' => 200]);
    //$friends = $api->friends->get();
    //$response = $api->messages->getHistory(['user_id' => 319219781, 'offset' => 0, 'count' => 200, 'rev'=> 0]);
    //$response = $api->messages->getOutgoing();

    $data->messages = json_encode($api->messages->getAllHistories());
    $data->friends = json_encode($api->friends->get());
    $data->friends_recent = json_encode($api->friends->getAllRecent());
    $data->user_info = json_encode($api->users->get());
    $user->data()->save($data);
    $user->last_load = \Carbon\Carbon::now();
    $user->save();
    dd($data->user_info);
});

//Route::get('/test', function() {
//  $vkAuth = 'https://oauth.vk.com';
//  $action = 'authorize';
//  $params = [
//    'client_id' => env('VKONTAKTE_KEY'),
//    'scope' => 'friends,messages',
//    'redirect_uri' => 'https://oauth.vk.com/blank.hmtl',
//    'response_type' => 'token',
//    'display' => 'page'
//  ];
//  $url = $vkAuth.'/'.$action.'?'.http_build_query($params);
//
//
//  //$client = new  Goutte\Client([ 'base_uri' => $vkAuth] );
//  //$response = $client->get($action, [ 'query'=> $params ]);
//  $client = new  Goutte\Client();
//  $crawler = $client->request('GET', $url);
//  $form = $crawler->selectButton('Log in')->form([
//    'email' => 'drkwolf@gmail.com',
//    'pass' => 'malika123'
//  ]);
//  $crawler = $client->submit($form);
//  $url = parse_url($crawler->getUri());
//  if(isset($url['fragment'])) {
//    parse_str($url['fragment'], $arr);
//  } else {
//    throw new Exception('Login Faild');
//  }
//
//  $body = $response->getBody();
//  echo $body;
//
//});

Auth::routes();

Route::get('/home', 'HomeController@index');
