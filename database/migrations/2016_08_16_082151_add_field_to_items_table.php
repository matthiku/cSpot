<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            // add field to make item visible only for plan leader
            $table->boolean('forLeadersEyesOnly')->default(0);
            $table->boolean('show_comment')->default(0);
            $table->dateTime('reported_at')->nullable();
            $table->boolean('hideTitle')->nullable();
            $table->integer('song_freshness')->nullable();
        });
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
            $table->dropColumn('forLeadersEyesOnly');
            $table->dropColumn('show_comment');
            $table->dropColumn('reported_at');
            $table->dropColumn('hideTitle');
            $table->dropColumn('song_freshness');
        });
    }
}
