<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProjectFormsAddMissingFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_forms', function (Blueprint $table) {
            $table->boolean('additional_notes_show')->after('sort_id')->default(false);
            $table->boolean('footer_text_show')->after('sort_id')->default(false);
            $table->text('footer_text', 65000)->after('sort_id')->nullable()->default(null);
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
            $table->dropColumn('additional_notes_show');
            $table->dropColumn('footer_text_show');
            $table->dropColumn('footer_text');
        });
    }
}
