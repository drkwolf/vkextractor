<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('stats', function (Blueprint $table) {
        $table->increments('id');
        $table->unsignedInteger('user_id')->unique();
        $table->json('success')->nullable();
        $table->json('fails')->nullable();
        $table->json('iter')->nullable();
        $table->json('results')->nullable();
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
        Schema::dropIfExists('stats');
    }
}
