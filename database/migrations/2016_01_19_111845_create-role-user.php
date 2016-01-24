<?php

/**
 * Create table for user-roles-relation
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoleUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('role_id')->unsigned()->index();
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('no action');
            $table->timestamps();
        });
        // set some default values
        DB::table('role_user')->insert(['user_id'=>1, 'role_id'=>'1']);
        DB::table('role_user')->insert(['user_id'=>1, 'role_id'=>'2']);
        DB::table('role_user')->insert(['user_id'=>1, 'role_id'=>'3']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('role_user');
    }
}
