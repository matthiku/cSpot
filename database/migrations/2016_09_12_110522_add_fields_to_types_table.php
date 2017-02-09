<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('types', function (Blueprint $table) {
            // default leader and resource for this plan
            $table->integer('leader_id')->unsigned()->nullable();
            $table->foreign('leader_id')->references('id')->on('users');
            $table->integer('resource_id')->unsigned()->nullable();
            $table->foreign('resource_id')->references('id')->on('resources');
            $table->integer('weekday')->nullable();
            $table->text('subtitle')->nullable();
            $table->boolean('generic');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('types', function (Blueprint $table) {
            //
            $table->dropColumn('weekday');
            $table->dropColumn('subtitle');
            $table->dropColumn('generic');
        });
        Schema::enableForeignKeyConstraints();
    }
}
