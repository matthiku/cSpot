<?php

/**
 * Create table for user roles
 * e.g. Admin and normal user
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->boolean('for_events')->default(true);
            $table->timestamps();
        });
        
        // insert default access rights roles 1=admin, 2=editor, 3=author!
        DB::table('roles')->insert(['name'=>'administrator', 'for_events'=>0]);
        DB::table('roles')->insert(['name'=>'editor', 'for_events'=>0]);
        DB::table('roles')->insert(['name'=>'author', 'for_events'=>0]);
        // insert real life roles
        DB::table('roles')->insert(['name'=>'teacher']);
        DB::table('roles')->insert(['name'=>'leader']);
        DB::table('roles')->insert(['name'=>'retired', 'for_events'=>0]);
        DB::table('roles')->insert(['name'=>'user', 'for_events'=>0]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('roles');
    }
}
