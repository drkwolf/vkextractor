<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 10/28/16
 * Time: 3:44 PM
 */

namespace App\VK\Api\Params;


class MessagesGetHistoryParams extends Paramameters
{

    public $default = [
    'user_id' => '',
    'offset' => '',
    'count' => '',
    'peer_id' => '',
    'start_message_id' => '',
    'rev' => ''
    ];
}