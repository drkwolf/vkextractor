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

  public function test_get_all_albums()
  {
    $result = $this->api->photos->getAlbums(['owner_id' => $this->user_id]);
    $this->saveTo($result, $this->user_id, 'albums');
    $this->assertEquals($result['count'], sizeof($result['items']));
  }

  public function test_get_likes()
  {
    $photos = $this->getArray($this->user_id, 'photos');
    $result = $this->api->photos->getAllLikesFromPhoto($photos);
    $this->saveTo($result, $this->user_id, 'photo_likes');
    $this->assertEquals($result['count'], sizeof($result['items']));
  }

  public function test_get_comments()
  {
    $albums = $this->getarray($this->user_id, 'albums');
    $result = $this->api->photos->getAllCommentsFromAlbums($albums);
    $this->saveTo($result, $this->user_id, 'photo_comments');
    $this->assertEquals($result['count'], sizeof($result['items']));
  }

  public function test_get_likes_from_photo_comments(){
    $comments = $this->getarray($this->user_id, 'photo_comments');
    $result = $this->api->likes->getLikesFromComments($comments, $owner_id=$this->user_id, $type='photo_comment');
    $this->saveTo($result, $this->user_id, 'photo_comments_likes');
    $this->assertEquals($result['count'], sizeof($result['items']));
  }

}
