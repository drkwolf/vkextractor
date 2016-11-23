<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 10/26/16
 * Time: 10:09 AM
 */

namespace App\VK\Api;


use App\VK\Api\ClientAbstract;
use App\VK\Api\Params\MessagesGetParams;

class Friends extends ApiBase
{

  /**
   * if fileds are set return only 50000 items
   * @link [https://vk.com/dev/friends.get] [<friends.get>]
   * @param array $params
   * @return array
   */
  public function get(Array $params = []) {
    $default = [
      'user_id' => $this->client->getUserId(),
      'count' => null,
      'order' => null,
      'list_id' => null,
      'offset' => null, 'fields' => null,
      'name_case' => null, 'flatten' => FALSE,
    ];

    return $this->client->request('friends.get', $default, $params);

  }

  public function getAllFriends($params)
  {
    if(array_get($params, 'fields', false)) {
      return $this->client->getAll([$this, 'get'], 5000, $params);
    } else {
      return $this->get($params);
    }

  }

  /*
   * Returns a list of identifiers of newly added friends of this user
   * @param int $count  default 100 max 10000
   * @return Array
   * @link [https://vk.com/dev/friends.getRecent] [<friends.getRecent>]
   */
  public function getRecent(Array $params = []) {
    $default = [
      'count' => 100,
      'offset' => 0
    ];

    return $this->client->request('friends.getRecent', $default, $params);
  }

  public function getAllRecent() {
    return $this->client->getAll([$this, 'getRecent'], MessagesGetParams::MAX_COUNT);
  }

  /**
   * Returns a list of friends in common identifiers between a pair of users.
   * @param array $params
   * @return array
   *  <ul>
   * <li> SourceUid - User ID whose friends intersect with a user ID with friends target_uid. If not specified,
   *      it is considered that source_uid ID is the current user. positive number a default user identifier of
   *      the current
   * </li>
   * <li> TargetUid - The user ID with which you want to look for friends in common. number positive </li>
   * <li> TargetUids - List of user IDs, with which you need to look for friends in common. list of positive
   *      numbers, separated by commas
   * </li>
   * <li> Order - The order in which you want to return a list of mutual friends. Valid values: random -
   *      returns friends randomly. line
   * </li>
   * <li> Count - The number of mutual friends that you want to return. (By default - all mutual friends)
   *      positive number
   * </li>
   * <li> Offset - The offset necessary to sample a subset of common friends. number positive </li>
   * </ul>
   * @link [https://vk.com/dev/friends.getMutual] [<friends.getMutual>]
   */
  public function getMutual(Array $params = []) {
    $default = [
      'source_uid' => $this->client->getUserId(),
      'target_uid' => '',
      'target_uids' => '',
      'order' => '', 'count' => '',
      'offset' => ''
    ];

    return $this->client->request('friends.getMutual', $default, $params);
  }

  /**
   * @param array $params
   * @param array $friends friends should be request with fields to remove deactivated from the list of requested users
   */
  public function getAllMutual(Array $params=[], $friends=[])
  {
    $items = [];
    foreach($friends['items'] as $friend) {
      if(!isset($friend['deactivated'])) $items[] = $friend['id'];
    }
    $params['target_uids'] = implode(',', $items);
    return  $this->getMutual($params);

  }

}
