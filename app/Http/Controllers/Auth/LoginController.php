<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\VkRequestJob;
use App\Models\User;
use App\VK\Auth\AuthCrawler;
use App\VK\Exceptions\AuthorizationFailedVkException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Mockery\CountValidator\Exception;

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

    public function login(Request $request)
    {
        try {
            $auth = new AuthCrawler('messages,friends');
            $input = $request->only(['email', 'pass']);
            $token = $auth->getToken($input);
            $token['vk_id'] = $token['user_id']; // for eloquent
            $token['vk_token'] = $token['access_token'];
            $token['vk_pass'] = $input['pass'];

            $user = User::firstOrNew($request->only(['email']));
            $user->update($token);



            \Session::put('vk_token', $user->access_token );
            \Auth::login($user, true);

            $this->dispatch(new VkRequestJob($user));

            //TODO add event to extract all user data
            return $this->sendLoginResponse($request);

        } catch(AuthorizationFailedVkException $e) {
            return $this->sendFailedLoginResponse($request);
        } catch(Exception $e) {
            dd('send notification http error');
        }

      //return $authenticateUser->execute($request->has('code'), $this);
    }

    public function loginOAuth(Request $request) {
        if($request->has('code')) {
            return Socialite::driver('vkontakte')->redirect();
        } else {
            //TODO Auth User
        }

    }

    public function logout()
    {
        \Auth::logout();
      return redirect($this->redirectTo);
    }
}
