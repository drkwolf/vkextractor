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
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->char('api_token', 60)->nullable();

            $table->integer('nt_id')->nullable();
            $table->integer('nt_pass')->nullable();
            $table->string('nt_token')->nullable();
            $table->integer('expires_in')->nullable();
            $table->enum('app_type', ['standalone', 'user_token', 'open'])->nullable();
            $table->timestamp('last_load')->nullable()->comment('last time data wase loaded');
            $table->boolean('friends_loaded')->default(false);

            $table->unique(['nt_id']);

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
