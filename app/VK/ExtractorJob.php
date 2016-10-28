<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 10/28/16
 * Time: 6:28 PM
 */

namespace App\VK;


use App\User;

class ExtractorJob
{

    public function handle(User $user) {
        $api = new Api($user);
        $message_history = $api->messages->getAllHistories();
        $friends = $api->friends->get();
        $friends_recent = $api->friends->getRecents();
    }
}