<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TestVkApiOpen extends TestCase
{
  protected $user_id;
  /**
   * @var \App\VK\ApiStandalone $api
   */
  protected $api;

  public function setUp()
  {
    parent::setUp();
    // wall hided = 2, deleted 4
    $this->user_id = 1; // TODO generated random
    $this->user_id = 364878143; // TODO generated random
    $this->user_id = 280603942; // TODO generated random
    $this->user_id = 5; // TODO generated random
    //$this->api = new \App\VK\ApiWithNoToken();
    $auth = new \App\VK\Auth\AuthCrawler('messages,friends');
    $credential = ['email' => env('VK_EMAIL'), 'password' => env('VK_PASS')];
    $vkToken = $auth->getToken($credential);

    $user = new \App\Models\User();
    $user->valuesFromTokenResponse($vkToken);
    $this->api = new \App\VK\ApiStandalone($user);
  }

  /**
   * A basic test example.
   *
   * @return void
   */
  public function test_users_info()
  {
    $getParams = new \App\VK\Api\Params\UsersGetParams($this->user_id);
    $user_info = $this->api->users->get($getParams->toArray());
    $this->saveTo($user_info, $this->user_id, 'user_info');

    $deactivated = array_has($user_info, 'deactivated', false);
    $hidden = array_has($user_info, 'hidden', false);
//    $blacklisted = array_has($user_info, 'blacklisted', false);

    // deactivated users
    if ($deactivated) {
      dump('user id' . $this->user_id . ' deactivated');
      return;
    }

    $params = ['user_id' => $this->user_id];

    $sub = $this->_users_get_subscriptions();
    $this->saveTo($sub, $this->user_id, 'subscriptions');
    $fol = $this->_users_get_followers();
    $this->saveto($fol, $this->user_id, 'followers');

    // Friends
    $friends = $this->_friends_get();
    $this->saveto($friends, $this->user_id, 'friends');

    try { // need user flag
      $recent  = $this->api->friends->getAllRecent($params);
      $this->saveto($recent, $this->user_id, 'friends_recent');
    } catch (\Exception $e) {
      dump('get Recent Faild '.$e);
    }

    $wall = $this->api->wall->getAllWall(['owner_id' => $this->user_id]);
    $this->saveTo($wall, $this->user_id, 'wall');

    if (!$hidden) { // need user token
      $comments = $this->api->wall->getCommentsFromWall($wall);
      $this->saveTo($comments, $this->user_id, 'comments');

      $likes = $this->api->likes->getLikesFrom($wall, $type='post');
      $this->saveTo($likes, $this->user_id, 'likes');
    } else {
      dump('User with hidden flag');
    }
    // Group
    $groups = $this->api->groups->getAllGroups($params);
    $this->saveto($groups, $this->user_id, 'groups');

    $groups_members = $this->api->groups->getMembersFromGroups($groups);
    $this->saveto($groups_members, $this->user_id, 'groups_Members');

    // extract post and

//    $this->assertNotEmpty($user_info);
//    $this->assertNotEmpty($friends);
  }

  public function _friends_get()
  {
    $params = ['user_id' => $this->user_id];
    return $this->api->friends->get($params);
  }

  public function _users_get_subscriptions()
  {
    $params = ['user_id' => $this->user_id];
    return $this->api->users->getAllSubscriptions($params);
  }

  public function _users_get_followers()
  {
    $params = ['user_id' => $this->user_id];
    return $this->api->users->getAllFollowers($params);
  }

  public function _wall_get_posts($wall)
  {
    $posts = [];
    foreach ($wall['items'] as $item) {
      $post = array_except($item, ['text']);
      $post['post_id'] = $post['id'];
      $posts[]= $post;
    }
    return $posts;
  }

  public function _wall_get_comments($posts)
  {
    $comments = [];
    foreach ($posts as $post) {
      $comments[] = $this->api->wall->getAllComments($post);
      break;
    }
    return $comments;

  }

  public function saveTo(Array $data, $id, $filename)
  {
    $root = storage_path('app/data/'.$id);
    $path = $root . '/'. $filename . '.php';
    if (!is_dir($root)) mkdir($pathname = $root, $mode=0777, $recursive = true);

    file_put_contents($path, var_export($data, true));
  }


}
