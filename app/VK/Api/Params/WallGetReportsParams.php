<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 11/12/16
 * Time: 2:18 PM
 */

namespace App\VK\Api\Params;


class WallGetReportsParams extends Parameters
{
  const MAX_COUNT = 1000;

  protected $required = ['owner_id', 'post_id'];

}
