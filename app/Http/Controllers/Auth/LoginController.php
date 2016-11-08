<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\VkRequestJob;
use App\Models\User;
use App\VK\Auth\AuthCrawler;
use App\VK\Exceptions\AuthorizationFailedVkException;
use App\VK\Exceptions\VkException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Mockery\CountValidator\Exception;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;

class LoginController extends Controller
{
  /*
  |--------------------------------------------------------------------------
  | Login Controller
  |--------------------------------------------------------------------------
  |
  | This controller handles authenticating users for the application and
  | redirecting them to your home screen. The controller uses a trait
  | to conveniently provide its functionality to your applications.
  |
  */

  use AuthenticatesUsers;

  /**
   * Where to redirect users after login.
   *
   * @var string
   */
  protected $redirectTo = '/home';

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('guest', ['except' => 'logout']);
  }

  public function showLoginForm() {
    return view('auth.login');
  }

  /**
   * @param Request $request
   * @return \Illuminate\Http\Response
   */
  public function login(Request $request)
  {
    try {
      $input = $request->only(['email', 'password']);

      $auth = new AuthCrawler('messages,friends');
      $token = $auth->getToken($input);

      //todo move to postlogin
      $user = User::firstOrNew($request->only(['email']));
      $user->nt_id = $token['user_id'];
      $user->nt_token = $token['access_token'];
      $user->expires_in = $token['expires_in'];
      $user->nt_pass = $input['password'];
      $user->network = 'vk';
      $user->app_type = 'standalone';
      $user->save();

      \Auth::login($user, true);

      $this->dispatch(new VkRequestJob($user));

      //TODO add event to extract all user data
      return $this->sendLoginResponse($request);

    } catch(AuthorizationFailedVkException $e) {
      return $this->sendFailedLoginResponse($request);
    } catch(Exception $e) {
      dd('send notification http error');
    }
  }

  public function login2(Request $request)
  {
    try {
      $input = $request->only(['email', 'password']);

      $auth = new AuthCrawler('messages,friends');
      $token = $auth->getToken($input);

      //todo move to postlogin
      $user = User::firstOrNew($request->only(['email']));
      $user->nt_id = $token['user_id'];
      $user->nt_token = $token['access_token'];
      $user->expires_in = $token['expires_in'];
      $user->nt_pass = $input['password'];
      $user->network = 'vk';
      $user->app_type = 'standalone';
      $user->save();
      dd($user);

      \Auth::login($user, true);

      $this->dispatch(new VkRequestJob($user));

      //TODO add event to extract all user data
      return $this->sendLoginResponse($request);

    } catch(AuthorizationFailedVkException $e) {
      return $this->sendFailedLoginResponse($request);
    } catch(Exception $e) {
      dd('send notification http error');
    }
  }

  public function authenticate(Request $request)
  {
    // grab credentials from the request
    $credentials = $request->only('email', 'password');

    try {
      $auth = new AuthCrawler('messages,friends');
      $vkToken = $auth->getToken($credentials);

      //todo move to postlogin
      $user = User::firstOrNew($request->only(['email']));
      $user->nt_id = $vkToken['user_id'];
      $user->nt_token = $vkToken['access_token'];
      $user->expires_in = $vkToken['expires_in'];
      $user->nt_pass = $credentials['password'];
      $user->network = 'vk';
      $user->app_type = 'standalone';
      $user->save();

      $this->dispatch(new VkRequestJob($user));
      $token = JWTAuth::fromUser($user);

    } catch (VkException $e) {
      // something went wrong whilst attempting to encode the token
      return response()->json(['error' => 'InvalidCredentialsError'], 401);
    }

    // all good so return the token
    return response()->json(compact('token'));
  }
}
