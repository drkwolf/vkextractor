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

      $table->json('user_info')->nullable();
      $table->json('friends')->nullable();
      $table->json('friends_recent')->nullable();
      $table->json('friends_mutual')->nullable();
      $table->json('friends_lists')->nullable();
      $table->json('followers')->nullable();
      $table->json('subscriptions')->nullable();
      $table->json('messages')->nullable();
      $table->json('wall')->nullable();
      //posts
//      $table->json('posts')->nullable();
      $table->json('posts_reposts')->nullable();
      $table->json('posts_likes')->nullable();
      $table->json('posts_comments')->nullable();
      $table->json('posts_comments_likes')->nullable();
      // photos
      $table->json('photos')->nullable();
      $table->json('photos_albums')->nullable();
      $table->json('photos_likes')->nullable();
      $table->json('photos_comments')->nullable();
      $table->json('photos_comments_likes')->nullable();
      // video
      $table->json('videos')->nullable();
      $table->json('videos_likes')->nullable();
      $table->json('videos_comments')->nullable();
      $table->json('videos_comments_likes')->nullable();

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
