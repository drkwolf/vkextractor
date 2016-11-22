<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TestUserData extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testFrequency ()
    {
      $data = factory(App\Models\Data::class)->make();
      $data->where('messages.');
    }
}
