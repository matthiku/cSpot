<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->increments('id');
            $table->datetime('date');
            $table->integer('leader_id' )->unsigned()->index();
            $table->foreign('leader_id' )->references('id')->on('users')->onDelete('no action');
            $table->integer('teacher_id')->unsigned();
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('no action');
            $table->integer('type_id'   )->unsigned()->index();
            $table->foreign('type_id'   )->references('id')->on('types')->onDelete('restrict');
            $table->string('info', 2000);
            $table->tinyInteger('state');
            $table->string('changer');
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
        Schema::drop('plans');
    }
}
