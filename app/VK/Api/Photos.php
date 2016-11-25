<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 11/12/16
 * Time: 3:16 PM
 */

namespace App\VK\Api;


use App\VK\Api\Params\LikesGetListParams;

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

  public function getAllPhotos(Array $params) {
    $params['extended'] = 1;
    return $this->client->getAll2('photos.getAll', 200, $params);
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
//      'count'  => 1000, // no max set
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
    $params = [];
    foreach($albums['items'] as $album) {
      $params[] = [
        'id' => $album['id'],
        'album_id' => $album['id'],
        'owner_id' => $album['owner_id'],
        'need_likes' => true,
      ];
    }
    return $this->client->getAll3('photos.getAllComments', 100, $params);
  }

  public function getAllLikesFromPhoto(array $photos)
  {
    $params = [];
    foreach($photos['items'] as $photo) {
      if(array_get($photo, 'likes.count', -1) > 0)
        $params[] = ['id' => $photo['id'], 'type' => 'photo', 'item_id' => $photo['id'], 'owner_id' => $photo['owner_id']];
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
            'from_id' => $item['from_id'], 'type' => 'photo_comment', 'owner_id' => $owner_id];
        }
      }
    }

    return $this->client->getAll3('likes.getList', LikesGetListParams::MAX_COUNT, $items);
  }

}
