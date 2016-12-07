<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLyricsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('on_songs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('song_id')->unsigned()->index();
            $table->foreign('song_id')->references('id')->on('songs');
            $table->integer('song_part_id')->unsigned()->index();
            $table->foreign('song_part_id')->references('id')->on('song_parts');
            $table->text('text');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('on_songs');
    }
}
