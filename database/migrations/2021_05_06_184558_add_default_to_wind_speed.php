<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultToWindSpeed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meteos', function (Blueprint $table) {
            $table->decimal('wind_speed_09', 10, 2)->default(0)->charset(null)->nullable()->change();
            $table->decimal('wind_direction_09', 10, 2)->default(0)->charset(null)->nullable()->change();
            $table->decimal('wind_speed_27', 10, 2)->default(0)->charset(null)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meteos', function (Blueprint $table) {
            //
        });
    }
}
