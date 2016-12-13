<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVkIdToDataMining extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('data_mining', function (Blueprint $table) {
          $table->integer('vk_id')->nullable()->after('photo_50');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('data_mining', function (Blueprint $table) {
          $table->dropColumn('vk_id');
        });
    }
}
