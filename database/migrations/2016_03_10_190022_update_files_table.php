<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('files', function (Blueprint $table) {
            //
            $table->integer('item_id')->unsigned()->nullable();
            $table->foreign('item_id')->references('id')->on('songs')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('files', function (Blueprint $table) {
            //
            $table->dropColumn('item_id');
        });
    }
}
