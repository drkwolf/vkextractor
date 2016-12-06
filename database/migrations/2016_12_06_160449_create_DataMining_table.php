<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataMiningTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      schema::create('data_mining', function (blueprint $table) {
        $table->increments('id');
        //vk_id
        $table->integer('user_id');
        $table->boolean('visibility');
        $table->boolean('first_name');
        $table->boolean('last_name');
        $table->boolean('sex');
        $table->boolean('bdate');
        $table->boolean('city');
        $table->boolean('country');
        $table->boolean('home_town');
        $table->boolean('contacts');
        $table->boolean('about');
        //taste
        $table->boolean('tv');
        $table->boolean('movies');
        $table->boolean('music');
        $table->boolean('books');
        $table->boolean('quotes');
        $table->boolean('interests');
        $table->boolean('activities');

        $table->boolean('education');
        $table->boolean('universities');
        $table->boolean('schools');
        $table->boolean('relatives');

        $table->boolean('relation');
        $table->boolean('occupations');

        $table->boolean('political');
        $table->boolean('langs');
        $table->boolean('smoking');
        $table->boolean('alcohol');
        $table->boolean('live_main');
        $table->boolean('people_main');
        $table->boolean('religion');
        $table->boolean('inspired_by');
        $table->boolean('photo_50');

        $table->foreign('user_id')->references('id')->on('users');
        $table->timestamps();

      });
    }

    /**    //
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_mining');
    }
}
