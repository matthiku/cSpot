<?php

/**
 * Plan Team table
 * this table defines the team of users that are involved in a plan and their corresponding roles
 * it also allows for user registration and their availability for this specific plan
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanTeamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_team', function (Blueprint $table) {
            $table->increments('id');
            
            // add relations to plans and songs tables
            $table->integer('plan_id')->unsigned()->index();
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('restrict');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            // defines the specific role of this user in this plan
            $table->integer('role_id')->unsigned()->nullable();
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('restrict');

            // user registration
            $table->boolean('requested');
            // user confirmation
            $table->boolean('confirmed');
            
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
        Schema::drop('plan_team');
    }
}
