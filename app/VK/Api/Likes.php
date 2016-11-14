<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 11/12/16
 * Time: 4:37 PM
 */

namespace App\VK\Api;


use App\VK\Api\Params\LikesGetListParams;

class Likes extends ApiBase
{

  public function getList(Array $params = [])
  {
    $default = [
      'type' => 'post', // comment, photo, audio, video, note
      'owner_id' => $this->client->getUserId(), //required
      'item_id' => null, // required
      'count' => 1, // MAX 1000
      'offset' => 0,
      'filter' => 'likes', // all, copies :  returns information only about users who told their friends about the object
      'friends_only' => 0, // 1 for friends only
      'extended' => 0, // want user id only
    ];

    return $this->client->request('likes.getList', $default, $params);
  }

  public function getAllList(Array $params)
  {
    return $this->client->getAll([$this, 'getList'], LikesGetListParams::MAX_COUNT, $params);
  }

  /**
   * extract user list likes from wall posts
   * @param array $wall
   */
  public function getLikesFromWall(Array $wall, $except = []) {
    $cast = ['id' => 'item_id', 'post_type' => 'type'];
    return $this->getAllFrom([$this, 'getAllList'], $wall, $cast, $except);
  }

}
