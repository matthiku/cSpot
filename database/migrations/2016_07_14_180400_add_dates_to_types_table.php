<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDatesToTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('types', function (Blueprint $table) {
            // add default start and end times for plan types
            $table->time('start');
            $table->time('end');
            $table->text('repeat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('types', function (Blueprint $table) {
            //
            $table->dropColumn('start');
            $table->dropColumn('end');
            $table->dropColumn('repeat');
        });
    }
}
