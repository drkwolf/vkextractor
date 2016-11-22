<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('groups', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name');
        $table->string('screen_name')->nullable();
        $table->boolean('is_admin');
        $table->boolean('is_member');
        $table->boolean('is_closed');
        $table->string('type');
        $table->string('photo_50');
        $table->json('members')->nullable();
        $table->json('topics')->nullable();
        $table->json('topics_comments')->nullable();
        $table->json('topics_comments_likes')->nullable();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::dropIfExists('groups');
    }
}
