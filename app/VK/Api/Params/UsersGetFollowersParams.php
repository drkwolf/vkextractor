<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 11/11/16
 * Time: 11:09 AM
 */

namespace App\VK\Api\Params;


class UsersGetFollowersParams extends Parameters
{
  const MAX_COUNT = 1000;

  protected  $required = ['user_id'];

  protected $default = [
    'user_id' => 0, //required
    'count' => 1, // Max 100
    'offset' => 0,
    'fields' => [],
    'extended' => 1,
    'name_case', 'nom'
  ];

}
