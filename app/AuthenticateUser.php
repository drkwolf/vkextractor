<?php
namespace App;

use Laravel\Socialite\Contracts\Factory as Socialite;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Auth\Guard;


class AuthenticateUser {

  /**
   *      * @var UserRepository
   *           */
  private $users;
  /**
   *      * @var Socialite
   *           */
  private $socialite;
  /**
   *      * @var Guard
   *           */
  private $guard;

  private $token;

  public function __construct(UserCircle $users, Socialite $socialite, Guard $guard)
  {

    $this->users = $users;
    $this->socialite = $socialite;
    $this->guard = $guard;

  }
  /**
   * @param $hasCode
   * @param AuthenticateUserListener $listener
   * @return mixed
   */
  public function execute($hasCode, AuthenticateUserListener $listener)
  {

    if ( ! $hasCode ) return $this->getAuthorizationFirst();

    $vkUser = $this->getVKontactUser();

    $user = $this->users->findByUsernameOrCreate($vkUesr);

    \Session::put('vk_token', $vkUser->token );

    \Auth::login($user, true);

    return $listener->userHasLoggedIn($user);

  }

  private function getAuthorizationFirst()
  {
    return \Socialize::with('vkontact')->redirect();
  }

  private function getVKontactUser()
  {
    return \Socialize::with('vkontact')->user();
  }

  /**
   * @param $email String email or phone number
   * @param $pass String
   * @param $scope default friends, should be sperated by coma
   * @details
   * List of Available Settings of \href{https://vk.com/dev/permissions}{Access Permissions}:
   * \itemize{
   *   \item \strong{friends} Access to friends.
   *   \item \strong{photos} Access to photos.
   *   \item \strong{audio} Access to audios.
   *   \item \strong{video} Access to videos.
   *   \item \strong{docs} Access to documents.
   *   \item \strong{notes} Access to user notes.
   *   \item \strong{pages} Access to wiki pages.
   *   \item \strong{status} Access to user status.
   *   \item \strong{wall} Access to standard and advanced methods for the wall.
   *   \item \strong{groups} Access to user groups.
   *   \item \strong{messages} Access to advanced methods for messaging.
   *   \item \strong{notifications} Access to notifications about answers to the user.
   * }
   * @return Array access_token, expires_in, user_id
   * @throw AuthorizationFailedVkException
   */
  private function getTokenByCridetential($email, $password, $scope='friends') {
    $vkAuth = 'https://oauth.vk.com/authorize';
    $params = [ 
      'client_id' => env('VKONTAKTE_KEY'),
      'scope' => $scopes,
      'redirect_uri' => 'https://oauth.vk.com/blank.hmtl', 
      'response_type' => 'token', 
      'display' => 'page'
    ];
    $url = $vkAuth.'?'.http_build_query($params);

    //TODO Validate email password required and not empty
    //TODO check timeout and 

    //Goutte and Guzzle (has beautilful function)
    $client = new  Goutte\Client();
    $crawler = $client->request('GET', $url);
    // fill vk login form
    $form = $crawler->selectButton('Log in')
      ->form([ 'email' => $email, 'pass' => $password ])
      ;
    $crawler = $client->submit($form);
    // get redirect uri that contain token
    $uri = parse_url( ($crawler->geturi()));

    // token are in the fragment
    if(array_get($uri, 'fragment', false)) {
      parse_str($uri['fragment'], $tokens);
    } else {
      throw new exception('login faild');
    }


    return $tokens;

  }

  private function getTokenByURL($uri){
    $uri = parse_url($uri);
    //TODO validate url pattern

    // token are in the fragment
    if(isset($uri['fragment'])) {
      parse_str($uri['fragment'], $tokens);
    } else {
      throw new exception('login faild');
    }

    return $tokens;

  }

  /**
   * retrive User's Friend and save theme
   *
   * list of method https://vk.com/dev/friends
   * using acttulalyy get and getRecent (need usertoken)
   * @param $method String  freinds method
   * @param $params Array method's parameter
   * @retrun ORM 
   */
  public function getFriends($method='get', $params = [])
  {
    $method = 'friends.'.$method;
    $params =  http_build_query($params);
  }
}
