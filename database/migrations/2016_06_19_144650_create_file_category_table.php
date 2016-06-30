<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
        // default value for existing images
        DB::insert('insert into file_categories (id, name) values (?, ?)', [1, 'songs']);
        DB::insert('insert into file_categories (id, name) values (?, ?)', [2, 'unset']);
        // for some reason, the above statement doesn't allow us to create a new record with the id of '0'!
        //  so we just execute an update
        DB::table('file_categories')->where('id',2)->update(['id'=>0]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('file_categories');
    }
}
