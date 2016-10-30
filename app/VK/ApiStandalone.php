<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 10/26/16
 * Time: 7:06 PM
 */

namespace App\VK;


use App\Models\User;
use App\VK\Api\ClientStandalone;
use App\VK\Api\Friends;
use App\VK\Api\Messages;
use App\VK\Api\Params\MessagesGetDiablogsParams;
use App\VK\Api\Params\MessagesGetHistoryParams;
use App\VK\Api\Params\MessagesGetParams;
use App\VK\Api\Users;

class ApiStandalone
{
    public $friends;
    public $messages;
    public $users;


    public function __construct(User $user) {
        $client = new ClientStandalone($user);
        $this->friends = new Friends($client);
        $this->messages = new Messages($client);
        $this->users = new Users($client);
    }

}