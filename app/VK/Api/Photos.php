<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 11/12/16
 * Time: 3:16 PM
 */

namespace App\VK\Api;


class Photos extends ApiBase
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
      'album_id' => null, // required
      'photo_ids' => null, // required
      'offset' => 0,
      'count'  => 1000, // MAX 1000
      'rev' => 0, // 1 rev 0 chrono
      'extended' => 1, // 1 likes, comments, tags
      'feed_type',
      'feed',
      'photo_size' => 1,
    ];

    return $this->client->request('photos.get', $default, $params);
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
      'count'  => 200, // MAx 200
      'extended' => true, // 1 likes, comments, tags
      'photo_size' => true,
      'need_hidden' => true,
    ];

    return $this->client->request('photos.getAll', $default, $params);
  }

  public function getAllPhotos(Array $params = []) {
    return $this->client->getAll([$this, 'getAll'], 200, $params);
  }

  /*
   * @param Array $params
   * @return Array
   *    aid — Album ID.
   *    thumb_id — ID of the album cover photo.
   *    owner_id — ID of the user or community that owns the album.
   *    title — Album title.
   *    description — Album description.
   *    created — Date (in Unix time) the album was created.
   *    updated — Date (in Unix time) the album was last updated.
   *    size — Number of photos in the album.
   *    privacy — Privacy settings for viewing the album.
   *    thumb_src — (If need_covers was specified) Link to the album cover photo.
   */
  public function getAlbums(Array $params = []) {
    $default = [
      'owner_id'  => null, // required
      'album_ids' => null,
      'photo_ids' => null,
      'offset' => 0,
      'count'  => 1000, // no max set
      'need_system' => false, // 1 rev 0 chrono
      'need_recovers' => false, // 1 likes, comments, tags
      'photo_sizes' => true,
    ];

    return $this->client->request('photos.getAlbums', $default, $params);

  }

//  https://vk.com/dev/photos.getAllComments

  public function getComments(array $params=[])
  {
    $default = [
      'owner_id'  => null, // required
      'photo_id' => null,
      'offset' => 0,
      'count'  => 100, // no max set
      'need_system' => false, // 1 rev 0 chrono
      'start_comment_id' => null,
      'sort' => 'asc',
      'need_likes' => true,
      'access_key' => null,
      'fields' => null,
    ];

    return $this->client->request('photos.getComments', $default, $params);
  }

  public function getAllCommentsApi(Array $params=[])
  {
    $default = [
      'owner_id'  => null, // required
      'album_id' => null,
      'offset' => 0,
      'count'  => 100, // no max set
      'need_likes' => true,
    ];

    return $this->client->request('photos.getAllComments', $default, $params);
  }

  public function getAllComments(Array $params=[])
  {
    return $this->client->getAll([$this, 'getAllCommentsApi'], 100, $params);
  }


  public function getAllCommentsFromAlbums(Array $albums)
  {
    $cast = ['id' => 'album_id'];
    return $this->getAllFrom([$this, 'getAllComments'], $albums , $cast);
  }
}
