<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEquipmentTableProjectId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('equipments', function ($table) {
            $table->integer('project_id')->unsigned()->nullable()->default(null)->after('company_id');

            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onDelete('cascade');
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

        Schema::table('equipments', function ($table) {
            $table->dropForeign('equipments_project_id_foreign');
            $table->dropColumn('project_id');
        });

        Schema::enableForeignKeyConstraints();
    }
}
