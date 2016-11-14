<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('data', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('user_id');

      $table->json('user_info');
      $table->json('friends');
      $table->json('friends_recent');
      $table->json('followers');
      $table->json('subscriptions');
      $table->json('messages');
      $table->json('wall');
      $table->json('reports');
      $table->json('posts');
      $table->json('comments');
      $table->json('likes');

      $table->foreign('user_id')->references('id')->on('users');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('data');
  }
}
