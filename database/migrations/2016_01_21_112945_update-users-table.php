<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // rename name to first_name
            $table->string('first_name');
            // add last_name field
            $table->string('last_name');
            // add last login date
            $table->timestamp('last_login');
            // add last access date
            $table->timestamp('last_access');
            // user to be notified on internal messages
            $table->boolean('notify_by_email')->default(true);
            
            // make email address field nullable, but unique if present
            $table->string('email')->nullable()->change();
            $table->unique('email');
        });
        DB::table('users')->insert(['id'=>0, 'first_name'=>'none', 'last_name'=>'none']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // rename first_name field back to name
            $table->dropColumn('first_name');
            // remove last_name field
            $table->dropColumn('last_name');

            $table->dropColumn('last_login');
            $table->dropColumn('last_access');
            
            $table->dropColumn('notify_by_email');
        });
    }
}
