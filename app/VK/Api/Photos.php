<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 11/12/16
 * Time: 3:16 PM
 */

namespace app\VK\Api;


class Photos
{
 protected $client;

  public function __construct(ClientAbstract $client) {
    $this->client = $client;
  }

  /**
   * public data available
   * requires user token for private
   * @param array $params
   * @return array
   */
  public function get(Array $params)
  {
    $default = [
      'owner_id'  => null, // required
      'album_id' => null,
      'photo_ids' => null, // required
      'offset' => 0,
      'count'  => 1,
      'rev' => 0, // 1 rev 0 chrono
      'extended' => 1, // 1 likes, comments, tags
      'feed_type',
      'feed',
      'photo_size' => 1,
    ];

    return $this->client->request('photo.get', $default, $params);
  }

  /**
   * Needs userToken
   * @param array $params
   * @return array
   */
  public function getAll(array $params)
  {
    $default = [
      'owner_id'  => null, // required // default current user
      'offset' => 0,
      'count'  => 1,
      'extended' => 1, // 1 likes, comments, tags
      'photo_size' => 1,
      'need_hidden' => 1,
    ];

    return $this->client->request('photo.get', $default, $params);
  }

//  https://vk.com/dev/photos.getAllComments
}
