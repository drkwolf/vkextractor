<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 10/26/16
 * Time: 7:06 PM
 */

namespace App\VK;


use App\Models\User;
use App\VK\Api\Board;
use App\VK\Api\ClientStandalone;
use App\VK\Api\Friends;
use App\VK\Api\Groups;
use App\VK\Api\Likes;
use App\VK\Api\Messages;
use App\VK\Api\Params\MessagesGetDiablogsParams;
use App\VK\Api\Params\MessagesGetHistoryParams;
use App\VK\Api\Params\MessagesGetParams;
use App\VK\Api\Photos;
use App\VK\Api\Users;
use App\VK\Api\Videos;
use App\VK\Api\Wall;

class ApiStandalone
{
  public $client;

  public $friends;
  public $messages;
  public $users;
  public $wall;
  public $likes;
  public $groups;
  public $boards;
  public $videos;


  public function __construct(User $user) {
    $this->client = $client = new ClientStandalone($user);
    $this->friends = new Friends($client);
    $this->messages = new Messages($client);
    $this->users = new Users($client);
    $this->wall = new Wall($client);
    $this->likes = new Likes($client);
    $this->groups = new Groups($client);
    $this->boards = new Board($client);
    $this->photos = new Photos($client);
    $this->videos = new Videos($client);
    }

}
