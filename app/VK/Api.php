<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 10/26/16
 * Time: 7:06 PM
 */

namespace App\VK;


use App\User;
use App\VK\Api\Friends;
use App\VK\Api\Messages;
use App\VK\Api\Params\MessagesGetDiablogsParams;
use App\VK\Api\Params\MessagesGetHistoryParams;
use App\VK\Api\Params\MessagesGetParams;
use App\VK\Api\Users;

class Api
{
    public $friends;
    public $messages;
    public $users;


    public function __construct(User $user) {
        $this->friends = new Friends($user);
        $this->messages = new Messages($user);
        $this->users = new Users($user);
    }

}