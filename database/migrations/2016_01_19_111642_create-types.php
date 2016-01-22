<?php

/**
 * Create table for user types
 * e.g. Admin and normal user
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('types', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->timestamps();
        });
        // set some default values
        DB::table('types')->insert(['id'=>0, 'name'=>'Regular Sunday Service']);
        DB::table('types')->insert(['name'=>'Family Sunday Service']);
        DB::table('types')->insert(['name'=>'Midweek Service']);
        DB::table('types')->insert(['name'=>'Midweek Bible Study']);
        DB::table('types')->insert(['name'=>'A.G.M.']);
        DB::table('types')->insert(['name'=>'Business Meeting']);
        DB::table('types')->insert(['name'=>'Other meeting']);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('types');
    }
}
