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
use App\VK\Api\Params\UsersGetParams;
use App\VK\Exceptions\UserDeletedOrBannedException;
use Illuminate\Support\Facades\Log;

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
//    $params['fields'] = implode(',', [ 'can_post', 'can_see_all_posts', 'can_see_audio', 'can_write_private_message', ]);
    if(array_has($params, 'fields')) {
      return $this->client->getAll2('friends.get', 5000, $params);
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
      'order' => 'hints', 'count' => '',
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
//    $friends = array_reverse($friends);
    foreach ($friends['items'] as $friend) {
      if (!array_has($friend, 'deactivated')) $items[] = $friend['id'];

    }

    if(sizeof($items) == 0) return [];

    $results = $this->getMutualFromInterval($items, $params);
    return $results;

//    $results = [];
//    $has_banned = false;
//    for ($i=0; $i<sizeof($items); $i++) {
//      try {
//        if ($has_banned) {
//          $params['target_uid'] = $items[$i];
//          $results[] = ['id' => $items[$i], 'common_friends' => $this->getMutual($params)];
//           dump('get user id '.$items[$i]);
//        } else {
//          dump('get remain '.key.' '.sizeof($items));
//          $remain = array_slice($items, $i);
//          $params['target_uids'] = implode(',', $remain);
//          $results[] += $this->getMutual($params);
//          return $results;
//          }
//      } catch (\Exception $e) {
//        $has_banned = !$has_banned;
//        dump('user banned '.$items[$i].' key '.$i);
////          dump('Mutual id probelem: ' . $params);
//        Log::info('user banned id : '.$items[$i]);
//        if($has_banned) $i--;
//      }
//    }
//      return $results;
  }

  /**
   * Vk has some awfull bug returning userbanned or delete
   * @param $items
   * @param array $params
   * @param array $results
   * @return array
   */
  public function getMutualFromInterval($items, $params = [], $results = []) {
    $len = sizeof($items);
    $start = floor($len/2);
    try {
      $params['target_uids'] = implode(',', $items);
      $res = $this->getMutual($params);
      if( $res == null) {
//        dump("        results" .sizeof($res).' '.$len);
        throw new UserDeletedOrBannedException();
      }

      $results = array_merge($results, $res);
//      Log::info('        len '. sizeof($results));
    } catch (UserDeletedOrBannedException $e) {
      if($start < 1) {
        dump($items[$start].' banned');
      }
      $remain = array_slice($items, 0, $start);
      $results = $this->getMutualFromInterval($remain, $params, $results);

      $remain = array_slice($items, $start);
      $results = $this->getMutualFromInterval($remain, $params, $results);
    }

    return $results;

  }

  public function getLists(array $params)
  {
     $default = [
      'user_id' =>  null,
       'return_system' => 0,
    ];

    return $this->client->request('friends.getLists', $default, $params);
  }

}
