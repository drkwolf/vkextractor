<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VKBoardTest extends VKBaseTest
{

  /**
   * A basic test example.
   *
   * @return void
   */
  public function test_getTopics()
  {
    $params = ['group_id' => 2657];

    $boards = $this->api->boards->getTopics($params);

    $this->assertArrayHasKey('count', $boards);
    $this->assertArrayHasKey('items', $boards);
    $this->assertEquals($boards['count'], sizeof($boards['items']));
  }


  public function test_get_groups()
  {
    $groups = $this->api->groups->getAllGroups(['user_id' => $this->user_id]);
    $this->saveTo($groups, $this->user_id, 'groups');
    $this->assertEquals($groups['count'], sizeof($groups['items']));
  }

  public function test_get_groups_members()
  {
    $groups = $this->getArray($this->user_id, 'groups');
    $result = $this->api->groups->getMembersFromGroups($groups);
    $this->saveTo($result, $this->user_id, 'groups_members');
    $this->assertEquals($result['count'], sizeof($result['items']));
  }

  public function test_get_boards_from_groups()
  {
    $groups = $this->api->groups->getAllGroups(['user_id' => $this->user_id]);
    $topics = $this->api->boards->getTopicsFromGroup($groups);
    $this->saveTo($topics, $this->user_id, 'topics');
    $this->assertEquals($topics['count'], sizeof($topics['items']));
  }

  public function test_get_comments() {
//    $groups = $this->api->groups->getAllGroups(['user_id' => 7830]);
//    $this->saveTo($groups, 0, 'groups');
//
//    $gtopics = $this->api->boards->getTopicsFromGroup($groups);
//    $this->saveTo($gtopics, 0, 'group_topics');
//
    $topics = $this->getArray($this->user_id, 'topics');
    $result = $this->api->boards->getCommentsFromTopics($topics);
    $this->saveTo($result, $this->user_id, 'topic_comments');

    $this->assertEquals($result['count'], sizeof($result['items']));

  }


  public function test_get_likes_from_topic_comments(){
//    $groups = $this->api->groups->getAllGroups(['user_id' => $this->user_id]);
//    $topics = $this->api->boards->getTopicsFromGroup($groups);
//    $comments = $this->api->boards->getCommentsFromTopics($topics);
    $comments = $this->getArray($this->user_id, 'topic_comments');
    $result = $this->api->likes->getLikesFromComments($comments, $owner_id=$this->user_id, $type='topic_comment');
    $this->saveTo($result, $this->user_id, 'topic_comments_likes');
    $this->assertEquals($result['count'], sizeof($result['items']));
  }

  public function test_get_members()
  {
    $groups = $this->api->groups->getAllGroups(['user_id' => $this->user_id]);
    $members = $this->api->groups->getAllMembers2(['group_id' => 29534144]);
    dump($members['count']);
    $this->assertEquals($members['count'], sizeof($members['items']));
  }
}
