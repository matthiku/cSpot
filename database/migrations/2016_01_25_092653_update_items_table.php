<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('items', 'plan_id')) {        
            Schema::table('items', function (Blueprint $table) {
                // add relations to plans and songs tables
                $table->integer('plan_id')->unsigned()->index();
                $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
                $table->integer('song_id')->unsigned()->nullable();
                $table->foreign('song_id')->references('id')->on('songs')->onDelete('no action');
                // `seq_no`, `comment`, `version`, `key
                $table->decimal('seq_no',3,1);
                $table->string('comment');
                $table->string('key');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            //
        });
    }
}
