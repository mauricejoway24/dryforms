<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProjectFormsTableAddTitleAndName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_forms', function (Blueprint $table) {
            $table->string('title')->after('project_id');
            $table->string('name')->after('title');
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
            $table->dropColumn('title');
            $table->dropColumn('name');
        });
    }
}
