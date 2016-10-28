<?php

namespace App\VK\Api\Params;

/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 10/27/16
 * Time: 4:32 PM
 */
class MessagesGetParams extends Paramameters
{
    const MAX_COUNT = 200;

    public $default = [
        'user_id' => '',
        'count' => 0,
        'offset' => '',
        'order' => '',
        'list_id' => '',
        'fields' => '',
        'name_case' => '',
        'flatten' => FALSE,
    ];

}