<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 11/12/16
 * Time: 5:47 PM
 */

namespace app\VK\Api;


class ApiBase
{
  protected $client;



  public function __construct(ClientAbstract $client) {
    $this->client = $client;
  }


  /**
   * the array must have this structure [ 'count' => xx, items=> [ 0=> [ id => .... ] ]]
   * @param array $wall
   * @param array $translate fields to be transalted
   * @param array $except id of element to exclude
   * @return array list of posts
   */
  protected function renameKeys(Array $wall, array $translate=[], array $except=[])
  {
    $posts = [];
//    if(!array_has($wall, 'items')) dump($wall);
    foreach ($wall['items'] as $item) {
      foreach ($translate as $key => $value) {
        if (is_array($value)) {// array when
          list($key, $value) = each($value);
          $item[$key] = $value;
        } else {
          if(!isset($item['id'])) dd($item);
          $item[$value] = $item[$key];
        }
      }
      if(!in_array($item['id'], $except))
        $posts[]= $item;
    }
    return $posts;
  }


  /**
   *  parse wall and extract all reports of a post_type
   * @param array $wall
   * @return array  items key is the id of the elements requested
   */
  public function getAllFrom(callable $callback , array $collect, Array $cast = [], Array $except = [])
  {
    $posts = $this->renameKeys($collect, $cast, $except);
    $items = [];

    foreach ($posts as $post) {
      // count = 1 when it doesn't exist
      if( (array_get($post, 'count', 1) > 0) AND !array_has($post, 'deactivated')) {
        $items[$post['id']] = call_user_func_array($callback, [$post]);
      }
    }

    return ['count' => sizeof($items), 'items' => $items];
  }
}
