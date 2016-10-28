<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->integer('vk_id')->nullable();
            $table->integer('vk_pass')->nullable();
            $table->string('vk_token')->nullable();
            $table->integer('expires_in')->nullable();
            $table->enum('app_type', ['standalone', 'user_token', 'open']);
            $table->timestamp('last_load')->nullable()->comment('last time data wase loaded');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
