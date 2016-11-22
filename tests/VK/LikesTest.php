<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VKLikesTest extends VKBaseTest
{

  /**
   * A basic test example.
   *
   * @return void
   */
  public function test_getList()
  {
    $params = ['item_id' => 2655, 'owner_id' => 5, 'type' => 'post'];

    $likes = $this->api->likes->getAllList($params);

    $this->assertArrayHasKey('count', $likes);
    $this->assertArrayHasKey('items', $likes);
    $this->assertEquals($likes['count'], sizeof($likes['items']));
  }

  public function test_get_list_from_wall()
  {
    $wall = $this->api->wall->get(['owner_id' => $this->user_id, 'count' => 5 ]);

    $likes = $this->api->likes->getLikesFrom($wall, $type='post', [2962]);

    $this->assertArrayHasKey('count', $likes);
    $this->assertArrayHasKey('items', $likes);
    $this->assertEquals($likes['count'], sizeof($likes['items']));
  }
}
