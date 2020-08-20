<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bis', function (Blueprint $table) {
            $table->id();
            $table->string("col0", 30)->nullable();
            $table->string("col1", 10);
            $table->string("col2", 10);
            $table->string("col3", 10);
            $table->string("col4", 10);
            $table->string("col5", 10);
            $table->string("col6", 10);
            $table->string("col7", 10);
            $table->string("col8", 10);
            $table->string("col9", 10);
            $table->string("col10", 10);
            $table->string("col11", 10);
            $table->string("col12", 10);
            $table->string("col13", 10);
            $table->string("col14", 10);
            $table->string("col15", 5)->nullable();
            $table->string("col16")->nullable();
            $table->string("col17")->nullable();
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
        Schema::dropIfExists('bis');
    }
}
