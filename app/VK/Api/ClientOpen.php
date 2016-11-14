<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 11/9/16
 * Time: 6:20 PM
 */

namespace App\VK\Api;


use App\VK\Api\ClientAbstract;

class ClientOpen extends ClientAbstract
{

  public function __construct($version)
  {
    parent::__construct($version);
  }
}
