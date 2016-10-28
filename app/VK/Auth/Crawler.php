<?php

use Goutte\Client;

/**
 * Authenticate user using Crawler client
 * required email/phone and password
 */
class AuthCrawler extends Auth {

  var $vkAuth = 'https://oauth.vk.com/authorize';

  public function __construct() {
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

    $client = new Client();
    $crawler = $client->request('GET', $url);
    // fill vk login form
    $form = $crawler->selectButton('Log in')
      ->form([ 'email' => $email, 'pass' => $password ]);
    $crawler = $client->submit($form);

    $uri = parse_url( ($crawler->geturi()));
    // token are in the fragment
    if(isset($uri['fragment'])) {
      parse_str($uri['fragment'], $tokens);
    } else {
      throw new exception('login faild');
    }


    return $tokens;

  }

}
