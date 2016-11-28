<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VKBaseTest extends TestCase
{
  protected $user_id;
  /**
   * @var \App\VK\ApiStandalone $api
   */
  protected $api;


  public function setUp()
  {
    parent::setUp();
    // wall hided = 2, deleted 4
    $this->user_id = 364878143; // TODO generated random
    $this->user_id = 280603942; // TODO generated random
//    $this->user_id = 5; // TODO generated random
    $this->user_id = 12; // TODO generated random
    //$this->api = new \App\VK\ApiWithNoToken();
    $auth = new \App\VK\Auth\AuthCrawler('messages,wall,friends,likes,photos,audio,video,pages,notes,groups,board,polls');
    $credential = ['email' => env('VK_EMAIL'), 'password' => env('VK_PASS')];
    $vkToken = $auth->getToken($credential);

    $user = new \App\Models\User();
    $user->valuesFromTokenResponse($vkToken);
    $this->api = new \App\VK\ApiStandalone($user);
  }


}
