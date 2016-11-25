<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 11/16/16
 * Time: 9:13 AM
 */

namespace tests\VK;


class VKVideosTest extends \VKBaseTest
{

  public function test_get_all_videos()
  {
    $result = $this->api->videos->getAllVideos(['owner_id' => $this->user_id]);
    $this->saveTo($result, $this->user_id, 'videos');
    $this->assertEquals($result['count'], sizeof($result['items']));
  }

  public function test_get_likes()
  {
    $videos = $this->getArray($this->user_id, 'videos');
    $result = $this->api->videos->getLikesFromVideos($videos);
    $this->saveTo($result, $this->user_id, 'videos_likes');
    $this->assertEquals($result['count'], sizeof($result['items']));
  }

  public function test_get_comments()
  {
    $videos = $this->getArray($this->user_id, 'videos');
    $result = $this->api->videos->getAllCommentsFromVideos($videos);
    $this->saveTo($result, $this->user_id, 'videos_comments');
    $this->assertEquals($result['count'], sizeof($result['items']));
  }

  public function test_get_likes_from_video_comments(){
    $comments = $this->getArray($this->user_id, 'videos_comments');
    $result = $this->api->videos->getLikesFromComments($comments, $owner_id=$this->user_id);
    $this->saveTo($result, $this->user_id, 'videos_comments_likes');
    $this->assertEquals($result['count'], sizeof($result['items']));
  }
}
