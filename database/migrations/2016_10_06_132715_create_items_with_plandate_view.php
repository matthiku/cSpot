<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsWithPlandateView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::statement("
                CREATE VIEW dated_items AS
                    SELECT items.id as id, items.song_id as song_id, items.plan_id as plan_id, plans.date as date
                        FROM items,plans
                        WHERE items.plan_id=plans.id;
            ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        DB::statement('DROP VIEW dated_items');
    }
}
