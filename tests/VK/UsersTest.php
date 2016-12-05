<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 11/16/16
 * Time: 9:13 AM
 */

namespace tests\VK;


class VKUsersTest extends \VKBaseTest
{

  public function test_get_followers()
  {
    $result = $this->api->users->getAllFollowers(['user_id' => '1']);
    $this->saveTo($result, $this->user_id, 'followers');
    $this->assertEquals($result['count'], sizeof($result['items']));
  }

  public function test_get_subscriptions() {
   $results = $this->api->users->getAllSubscriptions(['user_id', 2851806]) ;
  }

}
