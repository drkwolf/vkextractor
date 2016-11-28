<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 11/16/16
 * Time: 9:13 AM
 */

namespace tests\VK;


class VKFriendsTest extends \VKBaseTest
{

  public function test_get()
  {
    $result = $this->api->friends->get(['user_id' => $this->user_id]);
    $this->saveTo($result, $this->user_id, 'friends');
    $this->assertEquals($result['count'], sizeof($result['items']));
  }

  public function test_getRecents()
  {
    $result = $this->api->friends->get(['user_id' => $this->user_id]);
    $this->saveTo($result, $this->user_id, 'friends_recents');
    $this->assertEquals($result['count'], sizeof($result['items']));
  }

  public function test_getMutual()
  {
    $friends = $this->api->friends->getAllFriends(['user_id' => 113325095, 'fields' => ['deactivated']]);
//    $friends = $this->api->friends->get(['user_id' => $this->user_id, 'fields' => ['deactivated']]);
    $params = ['source_uid' => $this->user_id];
    $result = $this->api->friends->getAllMutual($params, $friends);
    $this->saveTo($result, $this->user_id, 'friends_mutual');
    $this->assertArrayHasKey($result, 'items');
  }



}
