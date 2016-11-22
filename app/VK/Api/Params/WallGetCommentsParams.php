<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 11/10/16
 * Time: 5:25 PM
 */

namespace App\VK\Api\Params;


class WallGetCommentsParams extends Parameters
{
  const MAX_COUNT = 100;

  protected $required = ['owner_id', 'post_id'];
}
