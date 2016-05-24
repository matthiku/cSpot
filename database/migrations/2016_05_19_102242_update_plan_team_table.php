<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePlanTeamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_team', function (Blueprint $table) {
            // user availability
            $table->boolean('available');
            // add comment field
            $table->text('comment');
            // add token field for direct confirmation (no login required)
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan_team', function (Blueprint $table) {
            // remove the fields again
            $table->dropColumn('comment');
            $table->dropColumn('available');
        });
    }
}
