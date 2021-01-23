<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeteosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meteos', function (Blueprint $table) {
            $table->id();
            $table->string('temperature', 30)->default('-');
            $table->string('wet', 30)->default('-');
            $table->string('wind_speed_09', 30)->default('-');
            $table->string('wind_direction_09', 30)->default('-');
            $table->string('bar', 30)->default('-');
//            $table->string('course_09', 30)->default('-');
//            $table->string('course_mid', 30)->default('-');
//            $table->string('course_27', 30)->default('-');
            $table->string('visibility_09', 30)->default('-');
            $table->string('visibility_mid', 30)->default('-');
            $table->string('visibility_27', 30)->default('-');
            $table->string('wind_speed_27', 30)->default('-');
            $table->string('wind_direction_27', 30)->default('-');
            $table->string('start_point', 30)->default('-');
            $table->string('weather', 30)->default('-');
            $table->string('contact_coefficient', 30)->default('-');
            $table->string('cloud_height', 30)->default('-');
            $table->string('cloudy', 30)->default('-');

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
        Schema::dropIfExists('meteos');
    }
}
