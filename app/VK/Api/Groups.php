<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 11/13/16
 * Time: 4:58 PM
 */

namespace App\VK\Api;


use App\VK\Api\Params\GroupsGetMembersParams;
use App\VK\Api\Params\GroupsGetParams;

class Groups extends ApiBase
{


  public function get(Array $params)
  {
    $default = [
     'user_id'  => null, // default current user
      'count' => 1, // MAX 1000
      'offset' => 0,
      'filter' => null, // look ath api
      'fields' => null,  // look at api
      'extended' => 1,
    ];

    return $this->client->request('groups.get', $default, $params);
  }

  public function getAllGroups(Array $params)
  {
   return $this->client->getAll([$this, 'get'], GroupsGetParams::MAX_COUNT, $params);
  }

  public function getMembers(Array $params) {
     $default = [
     'group_id'  => null, // default current user
      'count' => 1, // MAX 1000
      'offset' => 0,
      'filter' => null, // look ath api
       'fields' => ['photo_100', 'photo_200_orig', 'photo_200', 'photo_400_orig', 'photo_max', 'photo_max_orig',
         'online', 'online_mobile', 'lists', 'domain', 'has_mobile', 'contacts', 'connections', 'site',
         'education', 'universities', 'schools', 'can_post', 'can_see_all_posts', 'can_see_audio',
         'can_write_private_message', 'status', 'last_seen', 'common_count', 'relation',
         'relatives', 'counters'],  // look at api
       'extended' => 1,
    ];

    return $this->client->request('groups.getMembers', $default, $params);
  }

  public function getAllMembers(Array $params)
  {
    return $this->client->getAll([$this, 'getMembers'], GroupsGetMembersParams::MAX_COUNT, $params);
  }

  /**
   * // TODO only load groups that are not loaded
   * @param array $groups
   * @return array
   */
  public function getMembersFromGroups(array $groups, Array $except = []) {
    return $this->getAllFrom([$this, 'getAllMembers'], $groups,['id'=>'group_id'], $except);
  }
}
