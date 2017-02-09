<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('files', function (Blueprint $table) {
            // add file size in bytes
            $table->bigInteger('filesize')->unsigned();
            // add reference to file category
            $table->integer('file_category_id')->references('id')->on('file_category')->onDelete('no action');
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
        Schema::table('files', function (Blueprint $table) {
            // remove new column
            $table->dropColumn('filesize');
            $table->dropColumn('file_category_id');
        });
        Schema::enableForeignKeyConstraints();
    }
}
