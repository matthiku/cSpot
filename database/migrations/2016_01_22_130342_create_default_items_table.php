<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefaultItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('default_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type_id')->unsigned()->index();
            $table->foreign('type_id')->references('id')->on('types')->onDelete('no action');
            $table->integer('file_id')->unsigned()->nullable()->default(null);
            //$table->foreign('file_id')->references('id')->on('files')->onDelete('set null');
            $table->float('seq_no', 3, 1);
            $table->string('text', 250);
            $table->boolean('forLeadersEyesOnly')->default(false);
            $table->boolean('showItemText')->default(false);
            $table->boolean('showAnnouncements')->default(false);
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
        Schema::drop('default_items');
    }
}
