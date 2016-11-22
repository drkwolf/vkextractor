<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 11/19/16
 * Time: 6:37 PM
 */

namespace App\VK\Api;


class Videos extends ApiBase
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
      'videos' => null, // required
      'offset' => 0,
      'count'  => 1000, // MAX 1000
      'extended' => 1, // 1 likes, comments, tags
    ];

    return $this->client->request('video.get', $default, $params);
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
    ];

    return $this->client->request('video.getAll', $default, $params);
  }

  public function getAllVideos(Array $params = []) {
    return $this->client->getAll([$this, 'get'], 200, $params);
  }

  /*
   * @param Array $params
   * @return Array
   *    aid — Album ID.
   *    thumb_id — ID of the album cover video.
   *    owner_id — ID of the user or community that owns the album.
   *    title — Album title.
   *    description — Album description.
   *    created — Date (in Unix time) the album was created.
   *    updated — Date (in Unix time) the album was last updated.
   *    size — Number of video in the album.
   *    privacy — Privacy settings for viewing the album.
   *    thumb_src — (If need_covers was specified) Link to the album cover video.
   */
  public function getAlbums(Array $params = []) {
    $default = [
      'owner_id'  => null, // required
      'offset' => 0,
      'count'  => 100, // no max set
      'need_system' => false, // 1 rev 0 chrono
      'need_recovers' => false, // 1 likes, comments, tags
      'extended' => true, // 1 likes, comments, tags
    ];

    return $this->client->request('video.getAlbums', $default, $params);

  }

//  https://vk.com/dev/video.getAllComments

  public function getComments(array $params=[])
  {
    $default = [
      'owner_id'  => null, // required
      'video_id' => null,
      'offset' => 0,
      'count'  => 100, // no max set
      'need_system' => false, // 1 rev 0 chrono
      'start_comment_id' => null,
      'sort' => 'asc',
      'need_likes' => true,
    ];

    return $this->client->request('video.getComments', $default, $params);
  }


  public function getAllComments(Array $params=[])
  {
    return $this->client->getAll([$this, 'getComments'], 100, $params);
  }


  public function getAllCommentsFromVideos(Array $albums)
  {
    $cast = ['id' => 'video_id'];
    return $this->getAllFrom([$this, 'getAllComments'], $albums , $cast);
  }
}
