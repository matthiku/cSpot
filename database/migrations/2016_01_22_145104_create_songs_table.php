<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('title_2');
            $table->text('lyrics');
            $table->text('chords');
            $table->integer('ccli_no');
            $table->string('book_ref');
            $table->string('author');
            $table->enum('license', ['PD','CCLI', 'Other', 'Unknown'])nullable();
            $table->string('sequence');
            $table->string('youtube_id');
            $table->integer('hymnaldotnet_id');
            $table->string('link');
            $table->softDeletes(); // in order to retain DB integrity, songs are only marked as deleted
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
        Schema::drop('songs');
    }
}
