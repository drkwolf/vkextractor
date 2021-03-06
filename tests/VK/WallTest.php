<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VKWallTest extends VKBaseTest
{

  /**
   * A basic test example.
   *
   * @return void
   */
  public function test_get()
  {
    $wall = $this->api->wall->getAllWall2(['owner_id' => $this->user_id]);
    $this->saveTo($wall, $this->user_id, 'wall');
    $this->assertEquals($wall['count'], sizeof($wall['items']));
  }

  public function test_get_likes()
  {
    $wall = $this->getArray($this->user_id, 'wall');
    $result = $this->api->wall->getAllLikesFromWall($wall);
    $this->saveTo($result, $this->user_id, 'posts_likes');
    $this->assertEquals($result['count'], sizeof($result['items']));
  }


  public function test_get_reposts()
  {
    $wall = $this->getArray($this->user_id, 'wall');
    $result = $this->api->wall->getAllRepostsFromWall($wall);
    $this->saveTo($result, $this->user_id, 'wall_reports');
    $this->assertEquals($result['count'], sizeof($result['items']));
  }

    public function test_get_comments()
  {
    $wall = $this->getArray($this->user_id, 'wall');
    $result = $this->api->wall->getAllCommentsFromWall($wall);
    $this->saveTo($result, $this->user_id, 'wall_comments');
    $this->assertEquals($result['count'], sizeof($result['items']));
  }

  public function test_get_comments_likes()
  {
    $comments = $this->getArray($this->user_id, 'wall_comments');
    $result = $this->api->wall->getLikesFromComments($comments, $this->user_id);
    $this->saveTo($result, $this->user_id, 'wall_comments_likes');
    $this->assertEquals($result['count'], sizeof($result['items']));
  }


}
