<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDailyLogTableAddMissingColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('project_dailylogs', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->after('company_id');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('project_dailylogs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('project_dailylogs', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::enableForeignKeyConstraints();
    }
}
