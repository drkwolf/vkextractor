<?php

namespace App\VK\Api\Params;

/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 10/27/16
 * Time: 4:32 PM
 */
class MessagesGetParams extends Parameters
{

  public $default = [
    'user_id' => '',
    'count' => 0,
    'offset' => 0,
    'order' => '',
    'list_id' => '',
    'fields' => '',
    'name_case' => '',
    'flatten' => FALSE,
  ];



}
