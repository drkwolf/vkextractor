<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 10/28/16
 * Time: 6:41 PM
 */

namespace App\VK\Api;

use App\VK\Api\Params\UsersGetFollowersParams;
use App\VK\Api\Params\UsersGetSubscriptionsParams;

class Users extends ApiBase
{
  public $fields = [
        'sex', 'city', 'bdate', 'photo_50', 'nickname',
        'country', 'universities','verified',
        'home_town', 'education',
        'schools', 'common_count', 'personal', 'blacklisted',
        'occupation', 'contacts', 'site', 'counters', 'relatives', 'personal', 'relation',
        'connections', 'exports', 'interests', 'activities', 'music', 'movies', 'tv',
        'books', 'games', 'about', 'quotes', 'counters',
        'wall_comments', 'can_post', 'can_see_all_posts', 'can_see_audio', 'can_write_private_message',
        'timezone', 'screen_name'
      ];

  /**
   * @param array $params
   * @link [https://vk.com/dev/users.get]
   * @link [https://vk.com/dev/fields]
   * @return array
   */
  public function get(Array $params = []) {
    $default = [
      'user_ids' => null,
      'fields' => $this->fields,
      'name_case' => 'nom',
    ];

    $data = $this->client->request('users.get', $default, $params);

    return isset($data[0])? $data[0]: $data;
  }

  public function getAll($user_ids=[])
  {
    $len = sizeof($user_ids);
    $results = [];
    dump($len);
    for($i=0; $i < $len; $i+=1000) {
      $params = [
        'user_ids' => array_slice($user_ids, $i, 1000),
        'fields' => $this->fields,
      ];
//      dump(sizeof($params['user_ids']));
      $results =array_merge($results, $this->client->request('users.get', $params));
      dump(sizeof($results));
    }
    return $results;
  }


  public function getSubscriptions(Array $params = [])
  {
    $default = [
      'user_id' => $this->client->getUserId(), //required
      'count' => 1,
      'offset' => 0,
      'filter' => 'owner', // all, others
      'fields' => [],
      'extended' => 1,
    ];

    return $this->client->request('users.getSubscriptions', $default, $params);
  }

  public function getAllSubscriptions(Array $params = [])
  {
    return $this->client->getAll([$this, 'getSubscriptions'], UsersGetSubscriptionsParams::MAX_COUNT, $params);
  }

  public function getFollowers(array $params)
  {
     $default = [
      'user_id' => $this->client->getUserId(), //required
      'count' => 1, // Max 100
      'offset' => 0,
      'fields' => [],
       'name_case', 'nom'
    ];

    return $this->client->request('users.getFollowers', $default, $params);
  }


 public function getAllFollowers(array $params)
  {
    return $this->client->getAll2('users.getFollowers', UsersGetFollowersParams::MAX_COUNT, $params);
  }
}
