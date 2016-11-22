<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 11/16/16
 * Time: 9:13 AM
 */

namespace tests\VK;


class VKPhotosTest extends \VKBaseTest
{

  public function test_get_all_photos()
  {
    $result = $this->api->photos->getAllPhotos(['owner_id' => $this->user_id]);
    $this->saveTo($result, $this->user_id, 'photos');
    $this->assertEquals($result['count'], sizeof($result['items']));
  }

  public function test_get_likes()
  {
    $photos = $this->api->photos->getAllPhotos(['owner_id' => $this->user_id]);
    $result = $this->api->likes->getLikesFrom($photos, $type='photo');
    $this->saveTo($result, $this->user_id, 'photo_likes');
    $this->assertEquals($result['count'], sizeof($result['items']));
  }

  public function test_get_comments()
  {
    $albums = $this->api->photos->getAlbums(['owner_id' => $this->user_id]);
    $result = $this->api->photos->getAllCommentsFromAlbums($albums);
    $this->saveTo($result, $this->user_id, 'photo_comments');
    $this->assertEquals($result['count'], sizeof($result['items']));
  }

  public function test_get_likes_from_photo_comments(){
    $albums = $this->api->photos->getAlbums(['owner_id' => $this->user_id]);
    $comments = $this->api->photos->getAllCommentsFromAlbums($albums);
    $result = $this->api->likes->getLikesFromComments($comments, $owner_id=$this->user_id, $type='photo_comment');
    $this->saveTo($result, $this->user_id, 'photo_comments_likes');
    $this->assertEquals($result['count'], sizeof($result['items']));
  }
}
