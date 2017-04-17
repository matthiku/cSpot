<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBiblesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
            fields should be: 
                bibleversion_id, biblebook_id, chapter, verse, text
        */
        Schema::create('bibles', function (Blueprint $table) {
            $table->integer('bibleversion_id')->unsigned();
            $table->foreign('bibleversion_id')->references('id')->on('bibleversions');
            $table->integer('biblebook_id')->unsigned();
            $table->foreign('biblebook_id')->references('id')->on('biblebooks');
            $table->integer('chapter');
            $table->integer('verse');
            $table->string('text', 500);
            
            $table->index('bibleversion_id');
            $table->index('biblebook_id');
            $table->index('chapter');
            $table->index('verse');
            $table->index(['bibleversion_id', 'biblebook_id', 'chapter', 'verse'])->unique();
        });

        // add fulltext search index
        DB::raw('ALTER TABLE bibles ADD FULLTEXT INDEX `bibles_text_fulltext_index` (`text`);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bibles');
    }
}
