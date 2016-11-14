<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 11/10/16
 * Time: 2:26 PM
 */

namespace App\VK\Api\Params;


class UsersGetParams extends Parameters
{
  /**
   * Total user_ids that can be request at once
   */
  const MAX_USERS = 1000;

  // need user authenticated
  const RestrictedFields = [
    'common_count',
  ];

  const Fields = [
    'verified',
    'hidden',
    'blacklisted',
    'bdate',
    'contacts',
    'status',
    'last_seen',
    'sex',
    'photo_50',
    'city',
    'country',
    'home_town',
    'personal',
    'education',
    'schools',
    'occupation',
    'relatives',
    'relation',
    'nickname',
    'connections',
    'exports',
    'wall_comments',
    'activities',
    'interests',
    'music',
    'movies',
    'tv',
    'books',
    'games',
    'about',
    'quotes',
    'can_post',
    'can_see_all_posts',
    'can_see_audio',
    'can_write_private_message',
    'timezone',
    'screen_name',
  ];

  protected $default = [
    'user_ids' => null,
    'fields' => [],
    'name_case' => 'nom',
  ];

  // TODO if the user is authenticated ass common_count
  public function __construct($user_ids = null, array $fields = [], $name_case = null)
  {
    $this->params = array_merge($this->default, [
      'user_ids' => $user_ids,
      'fields' => empty($fields)? static::Fields: $fields,
      '$name_case' => $name_case? $this->default['name_case'] : $name_case
    ]);

  }

  /**
   * set user range from MAX_USER*i to MAX_USER*i+1
   * @param int $i
   */
  public function setUsersRange (int $i)
  {
    $this->default['user_ids'] = range(static::MAX_USERS*$i, static::MAX_USERS*($i+1));
  }
}
