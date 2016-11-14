<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 11/8/16
 * Time: 8:33 PM
 */

namespace app\VK;


use App\VK\Api\ClientOpen;
use App\VK\Api\Friends;
use App\VK\Api\Likes;
use App\VK\Api\Users;
use App\VK\Api\Wall;

class ApiWithNoToken
{
  public $users;
  public $friends;
  public $wall;
  public $likes;



  public function __construct(int $user_id = null) {
    $client = new ClientOpen($user_id);
    $this->users = new Users($client);
    $this->friends = new Friends($client);
    $this->wall = new Wall($client);
    $this->likes = new Likes($client);
  }
}
