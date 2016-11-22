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
      'count' => 1000, // MAX 1000
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
  public function getLikesFrom(Array $wall, $type, $except = []) {
    $cast = ['id' => 'item_id', ['type' => $type]];
    return $this->getAllFrom([$this, 'getAllList'], $wall, $cast, $except);
  }

  public function getLikesFromComments(Array $comments, $owner_id, $type) {

    $items= [];
    foreach($comments['items'] as $comment) {
     if($comment['count'] === 0 ) continue;
      foreach($comment['items'] as $item) {
        if($item['likes']['count']>0) {
          $items[] = ['item_id' => $item['id'],'id' => $item['id'],
            'from_id' => $item['from_id'], 'type' => $type, 'owner_id' => $owner_id];
        }
      }
    }
    return $this->getAllFrom([$this, 'getAllList'],
      ['count' => sizeof($items), 'items' => $items]);
  }

}
