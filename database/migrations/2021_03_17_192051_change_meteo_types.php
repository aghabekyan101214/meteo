<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMeteoTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meteos', function (Blueprint $table) {
            $table->decimal('temperature', 10, 2)->default(null)->charset(null)->nullable()->change();
            $table->decimal('wet', 10, 1)->default(null)->charset(null)->nullable()->change();
            $table->decimal('wind_speed_09', 10, 2)->default(null)->charset(null)->nullable()->change();
            $table->decimal('wind_direction_09', 10, 2)->default(null)->charset(null)->nullable()->change();
            $table->decimal('bar', 10, 2)->default(null)->charset(null)->nullable()->change();
            $table->decimal('visibility_09', 10, 2)->default(null)->charset(null)->nullable()->change();
            $table->decimal('visibility_mid', 10, 2)->default(null)->charset(null)->nullable()->change();
            $table->decimal('visibility_27', 10, 2)->default(null)->charset(null)->nullable()->change();
            $table->decimal('wind_speed_27', 10, 2)->default(null)->charset(null)->nullable()->change();
            $table->decimal('wind_direction_27', 10, 2)->default(null)->charset(null)->nullable()->change();
            $table->decimal('start_point', 10, 2)->default(null)->charset(null)->nullable()->change();
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
