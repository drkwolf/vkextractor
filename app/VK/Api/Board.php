<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 11/13/16
 * Time: 8:39 PM
 */

namespace App\VK\Api;


class Board extends ApiBase
{

  public function getTopics(Array $params)
  {
    $default = [
      'group_id' => null, //required
      'topic_ids' => [], //
      'order' => -2, // created in chron order
      'offset' => 0,
      'count' => 100, // Max 100
      'extended' => 0,
      'preview_length' => null,
      'preview' => 0, // no preview
    ];

    return $this->client->request('board.getTopics', $default, $params);
  }

  public function getAllTopics(Array $params) {
    return $this->client->getAll([$this, 'getTopics'], 100, $params);
  }

  public function getTopicsFromGroup(array $group, array $except=[])
  {
    return $this->getAllFrom([$this, 'getAllTopics'], $group, ['id' => 'group_id'], $except);
  }


  public function getComments(Array $params)
  {
    $default = [
      'group_id' => null, //required
      'topic_id' => null, // required
      'sort' => 'asc', // created in chron order
      'offset' => 0,
      'count' => 100, // Max 100
      'extended' => 0,
      'need_likes' => true,
      'start_comment_id' => null
    ];

    return $this->client->request('board.getComments', $default, $params);
  }

  public function getAllComments(Array $params) {
    return $this->client->getAll([$this, 'getComments'], 100, $params);
  }

  /**
   * @param array $gtopics
   * @return array count , items : topic_id => item
   */
  public function getCommentsFromTopics(array $gtopics)
  {
    $items = [];
    foreach($gtopics['items'] as $group_id => $topics) {
      foreach($topics['items'] as $topic)
        $items[] = ['group_id' => $group_id, 'id' => $topic['id'], 'topic_id' => $topic['id']];
    }
    $params = ['count' => sizeof($items), 'items' => $items];


    return $this->getAllFrom([$this, 'getAllComments'], $params);
  }


}
