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
  $user = \App\User::all()->first();
   $api = new \App\VK\Api($user);
    //$api->getAllMsgs();
    //$response = $api->messages->getIncoming(['count' => 200]);
    //$response2 = $api->friends->get(['count' => 100]);
    //$response = $api->messages->getHistory(['offset' => 0, 'count' => 200, 'rev'=> 0]);
    //$response = $api->messages->getOutgoing();
    $response = $api->getAllDialogs();
    dd($response);
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
