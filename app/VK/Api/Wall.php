<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 11/10/16
 * Time: 4:57 PM
 */

namespace App\VK\Api;


use App\VK\Api\Params\WallGetCommentsParams;
use App\VK\Api\Params\WallGetParams;

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
  public function getCommentsFromWall(Array $wall)
  {
    $posts = $this->renameKeys($wall, ['id' => 'post_id']);
    $items = [];
    $count = 0;
    foreach ($posts as $post) {
      if (array_get($post, 'comments.count', 0) > 0) {
        $count++;
        $items[$post['post_id']] = $this->getAllComments($post);
      }
    }

   return ['count' => $count, 'items' =>  $items];
  }

  public function getReports(array $params)
  {
    $default = [
      'owner_id' => $this->client->getUserId(), //required
      'post_id' => null,  //required
      'count' => 1, // Max 1000
      'offset' => 0,
    ];

    return $this->client->request('wall.getReports', $default, $params);
  }

  /**
   * get all reports for a post
   * @param array $params
   * @return array|mixed
   */
  public function getAllReports(array $params) {
    return $this->client->getAll([$this, 'getComments'], WallGetReportsParams::MAX_COUNT, $params);
  }

  /**
   *  parse wall and extract all reports of a post_type
   * @param array $wall
   * @return array
   */
  public function getAllReportsFromWall(array $wall)
  {
    $posts = $this->renameKeys($wall, ['id' => 'post_id']);
    $reports = ['count' => array_get($wall, 'count')];
    $items = [];
    foreach ($posts as $post) {
      $items[] = $this->getAllComments($post);
    }

    $reports['items'] =  $items;
    return $reports;
  }

}
