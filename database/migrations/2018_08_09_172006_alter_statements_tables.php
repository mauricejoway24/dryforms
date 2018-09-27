<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStatementsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('standard_statements', function (Blueprint $table) {
            $table->dropColumn('title');
        });

        Schema::table('standard_statements', function (Blueprint $table) {
            $table->string('title', 50)->nullable()->default(null)->after('statement');
        });

        Schema::table('project_statements', function (Blueprint $table) {
            $table->dropColumn('title');
        });

        Schema::table('project_statements', function (Blueprint $table) {
            $table->string('title', 50)->nullable()->default(null)->after('statement');
            $table->boolean('checked')->default(false)->after('title');
        });

        Schema::table('default_statements', function (Blueprint $table) {
            $table->dropColumn('title');
        });

        Schema::table('default_statements', function (Blueprint $table) {
            $table->string('title', 50)->nullable()->default(null)->after('statement');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
