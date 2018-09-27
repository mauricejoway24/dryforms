<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMoistureFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('project_moisture_days');
        Schema::dropIfExists('project_moisture_settings');

        Schema::create('moisture_forms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id')->unsigned();
            $table->timestamps();
        });

        Schema::create('moisture_form_days', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('moisture_form_id')->unsigned();
            $table->date('date');
        });

        Schema::create('moisture_form_day_data', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('day_id')->unsigned();
            $table->integer('area_id')->unsigned();
            $table->string('data', 2000);
        });

        Schema::table('moisture_forms', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });

        Schema::table('moisture_form_days', function (Blueprint $table) {
            $table->foreign('moisture_form_id')->references('id')->on('moisture_forms')->onDelete('cascade');
        });

        Schema::table('moisture_form_day_data', function (Blueprint $table) {
            $table->foreign('day_id')->references('id')->on('moisture_form_days')->onDelete('cascade');
            $table->foreign('area_id')->references('id')->on('project_areas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('moisture_forms');
        Schema::dropIfExists('moisture_form_days');
        Schema::dropIfExists('moisture_form_day_data');
    }
}
