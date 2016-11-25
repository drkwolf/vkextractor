<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 11/10/16
 * Time: 4:57 PM
 */

namespace App\VK\Api;


use App\VK\Api\Params\LikesGetListParams;
use App\VK\Api\Params\WallGetCommentsParams;
use App\VK\Api\Params\WallGetParams;
use App\VK\Api\Params\WallGetReportsParams;

class Wall extends ApiBase
{

  public function get(Array $params = [])
  {
    $default = [
      'owner_id' => $this->client->getUserId(), //required
      'count' => 1,
      'offset' => 0,
      'filter' => 'owner', // all, others
      'fields' => [],
      'extended' => 1,
    ];

    return $this->client->request('wall.get', $default, $params);
  }

  public function getAllWall(array $params)
  {
    return $this->client->getAll([$this, 'get'], WallGetParams::MAX_COUNT, $params);
  }
 public function getAllWall2(array $params)
  {
    return $this->client->getAll2('wall.get', WallGetParams::MAX_COUNT, $params, 15);
  }
  // 212 Access to post comments denied
  //
  // If need_likes is set to 1, returns an additional likes field containing the following:
  // count — Number of users who liked the comment.
  // user_likes — Whether the user liked the comment (0 — not liked, 1 — liked).
  // can_like — Whether the user can like the comment (0 — cannot, 1 — can).
  public function getComments(array $params) {
     $default = [
       'owner_id' => $this->client->getUserId(), //required
       'post_id' => null,//required
       'count' => 1, // Max 100
       'offset' => 0,
       'need_likes' => true,
       'start_comment_id' => null,
       'sort' => 'desc',
       'preview_length' => 10,
       'extended' => 1,
    ];

    return $this->client->request('wall.getComments', $default, $params);
  }

  public function getAllComments(array $params)
  {
    return $this->client->getAll([$this, 'getComments'], WallGetCommentsParams::MAX_COUNT, $params);
  }


  /**
   * get Comments form posts in the wall
   * @param array $wall
   * @return array $comments
   */
//  public function getCommentsFromWall(Array $wall)
//  {
//    $cast = ['id' => 'post_id'];
//    return $this->getAllFrom([$this, 'getAllComments'], $wall , $cast);
//  }
  public function getAllCommentsFromWall(array $walls)
  {
    $params = [];

    foreach($walls['items'] as $wall) {
      if(array_get($wall, 'comments.count', -1) > 0)
        $params[] = ['id' => $wall['id'],
          'post_id' => $wall['id'],
          'owner_id' => $wall['owner_id'],
          'need_likes' => true,
        ];
    }

    return $this->client->getAll3('wall.getComments', WallGetCommentsParams::MAX_COUNT, $params);
  }


  public function getReposts(array $params)
  {
    $default = [
      'owner_id' => $this->client->getUserId(), //required
      'post_id' => null,  //required
      'count' => 1, // Max 1000
      'offset' => 0,
    ];

    return $this->client->request('wall.getReposts', $default, $params);
  }

  /**
   * get all reports for a post
   * @param array $params
   * @return array|mixed
   */
//  public function getAllReposts(array $params) {
//    return $this->client->getAll([$this, 'getReposts'], WallGetReportsParams::MAX_COUNT, $params);
//  }
  public function getAllReposts(array $params) {
    return $this->client->getAll2('wall.getReposts', WallGetReportsParams::MAX_COUNT, $params);
  }
  /**
   *  parse wall and extract all reports of a post_type
   * @param array $wall
   * @return array
   */
////  public function getAllRepostsfromwall(array $wall)
////  {
////    $cast = ['id' => 'post_id'];
////    return $this->getallfrom([$this, 'getallreposts'], $wall , $cast);
//  }

  public function getAllRepostsFromWall(array $walls)
  {
    $params = [];
    foreach($walls['items'] as $wall) {
      if(array_get($wall, 'reposts.count', -1) > 0)
        $params[] = [
          'id' => $wall['id'],
          'item_id' => $wall['id'],
          'filter' => 'copies',
          'type' => 'post',
          'owner_id' => $wall['owner_id']
        ];
    }
    return $this->client->getAll3('likes.getList', LikesGetListParams::MAX_COUNT, $params);
  }

  public function getAllLikesFromWall(array $walls)
  {
    $params = [];
    foreach($walls['items'] as $wall) {
      if(array_get($wall, 'likes.count', -1) > 0)
        $params[] = ['id' => $wall['id'], 'type' => 'post', 'item_id' => $wall['id'], 'owner_id' => $wall['owner_id']];
    }
    return $this->client->getAll3('likes.getList', LikesGetListParams::MAX_COUNT, $params);
  }

  public function getLikesFromComments(Array $comments, $owner_id) {

    $items = [];
    foreach($comments['items'] as $comment) {
      if($comment['count'] === 0 ) continue;
      foreach($comment['items'] as $item) {
        if(array_get($item, 'likes.count', 0) > 0) {
          $items[] = ['item_id' => $item['id'],'id' => $item['id'],
            'from_id' => $item['from_id'], 'type' => 'comment', 'owner_id' => $owner_id];
        }
      }
    }

    return $this->client->getAll3('likes.getList', LikesGetListParams::MAX_COUNT, $items);
  }

}
