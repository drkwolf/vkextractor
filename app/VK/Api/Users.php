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

  /**
   * @param array $params
   * @link [https://vk.com/dev/users.get]
   * @link [https://vk.com/dev/fields]
   * @return array
   */
  public function get(Array $params = []) {
    $default = [
      'user_ids' => null,
      'fields' => ['sex', 'city', 'photo_50', 'country', 'universities','verified', 'home_town', 'education', 'universities',
        'schools', 'common_count', 'personal', 'blacklisted'],
      'name_case' => 'nom',
    ];

    $data = $this->client->request('users.get', $default, $params);

    return isset($data[0])? $data[0]: $data;
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
