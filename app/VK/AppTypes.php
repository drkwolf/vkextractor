<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 11/9/16
 * Time: 6:05 PM
 */

namespace app\VK;

/**
 * supported Vk application type
 * see
 * Class AppTypes
 * @package app\VK
 */
class AppTypes
{
  // acessing the Vk as api
  const STANDALONE = 'standalone';
  // accessing The vk without topen
  const OPEN = 'open';
  // using user Token
  const USERTOKEN = 'user_token';


  /**
   * return avaible type of application
   */
  public static function get() {
    return [ static::OPEN, static::STANDALONE, static::USERTOKEN ];
  }
}
