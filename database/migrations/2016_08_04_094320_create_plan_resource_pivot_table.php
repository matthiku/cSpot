<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanResourcePivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_resource', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('plan_id')->unsigned()->index();
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('no action');
            $table->integer('resource_id')->unsigned()->index();
            $table->foreign('resource_id')->references('id')->on('resources')->onDelete('no action');
            $table->text('comment');
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
        Schema::drop('plan_resource');
    }
}
