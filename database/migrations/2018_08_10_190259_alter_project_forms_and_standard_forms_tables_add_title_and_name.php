<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProjectFormsAndStandardFormsTablesAddTitleAndName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_forms', function (Blueprint $table) {
            $table->integer('sort_id')->after('name');
        });

        Schema::table('standard_forms', function (Blueprint $table) {
            $table->integer('sort_id')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_forms', function (Blueprint $table) {
            $table->dropColumn('sort_id');
        });

        Schema::table('standard_forms', function (Blueprint $table) {
            $table->dropColumn('sort_id');
        });
    }
}
