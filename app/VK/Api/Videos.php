<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 11/19/16
 * Time: 6:37 PM
 */

namespace App\VK\Api;


use App\VK\Api\Params\LikesGetListParams;

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
      'count'  => 200, // MAX 1000
      'extended' => 1, // 1 likes, comments, tags
    ];

    return $this->client->request('video.get', $default, $params);
  }

  public function getAllVideos(Array $params) {
    $params['extended'] = 1;
    return $this->client->getAll2('video.get', 200, $params);
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
    $params = [];
    foreach($albums['items'] as $album) {
      if(array_get($album, 'can_comment', 0)) {
        $params[] = [
          'id' => $album['id'],
          'video_id' => $album['id'],
          'owner_id' => $album['owner_id'],
          'need_likes' => true,
        ];
      }
    }
    return $this->client->getAll3('video.getComments', 100, $params);
  }

    public function getLikesFromVideos(array $videos)
  {
    $params = [];
    foreach($videos['items'] as $video) {
      if(array_get($video, 'likes.count', -1) > 0)
        $params[] = ['id' => $video['id'], 'type' => 'video', 'item_id' => $video['id'], 'owner_id' => $video['owner_id']];

    }
    return $this->client->getAll3('likes.getList', LikesGetListParams::MAX_COUNT, $params);
  }

  public function getLikesFromComments(Array $comments, $owner_id) {

    $items = [];
    foreach($comments['items'] as $comment) {
      if($comment['count'] === 0 ) continue;
      foreach($comment['items'] as $item) {
        if(array_get($item, 'likes.count', 0) > 0) {
          $items[] = ['item_id' => $item['id'],'id' => $item['id'],
            'from_id' => $item['from_id'], 'type' => 'video_comment', 'owner_id' => $owner_id];
        }
      }
    }

    return $this->client->getAll3('likes.getList', LikesGetListParams::MAX_COUNT, $items);
  }


}
