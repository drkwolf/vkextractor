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
      $table->json('friends_mutual');
      $table->json('followers');
      $table->json('subscriptions');
      $table->json('messages');
      $table->json('wall');
      //posts
      $table->json('posts');
      $table->json('posts_reposts');
      $table->json('posts_likes');
      $table->json('posts_comments');
      $table->json('posts_comments_likes');
      // photos
      $table->json('photos');
      $table->json('photos_albums');
      $table->json('photos_likes');
      $table->json('photos_comments');
      $table->json('photos_comments_likes');
      // video
      $table->json('videos');
      $table->json('videos_likes');
      $table->json('videos_comments');
      $table->json('videos_comments_likes');

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
