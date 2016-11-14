<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 11/10/16
 * Time: 10:16 PM
 */

namespace App\VK\Api\Params;


class UsersGetSubscriptionsParams extends Parameters
{
  const MAX_COUNT = 200;

  protected $required = ['user_id'];
}
