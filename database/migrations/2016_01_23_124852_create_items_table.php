<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            
            // add relations to plans and songs tables
            $table->integer('plan_id')->unsigned()->index();
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
            $table->integer('song_id')->unsigned()->nullable();
            $table->foreign('song_id')->references('id')->on('songs')->onDelete('no action');
            // `seq_no`, `comment`, `version`, `key
            $table->decimal('seq_no',3,1);
            $table->string('comment');
            $table->enum('version', ['ESV', 'NIV', 'NKJV', 'NASB', 'MSG', 'NLT', 'AMP', 'KJV', 'CEV']);
            $table->string('key');

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
        Schema::drop('items');
    }
}
