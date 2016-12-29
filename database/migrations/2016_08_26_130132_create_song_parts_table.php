<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSongPartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('song_parts', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('sequence'); 
            $table->string('code', 2);
            $table->text('name');
            $table->timestamps();
        });

        // We fill the table already with the bare minimum song parts needed
        $song_parts = array(
            array( 'sequence' => 0, 'name' => 'Metadata', 'code' => 'm',),
            array( 'sequence' => 1, 'name' => 'Intro',    'code' => 'i',),
            array( 'sequence' => 3, 'name' => 'Chorus',   'code' => 'c',),
            array( 'sequence' => 4, 'name' => 'Bridge',   'code' => 'b',),
            array( 'sequence' => 5, 'name' => 'Prechorus','code' => 'p',),
            array( 'sequence' => 6, 'name' => 'Chorus 2', 'code' => 't',),
            array( 'sequence' => 7, 'name' => 'Bridge 2', 'code' => 'r',),
            array( 'sequence' => 11, 'name' => 'Verse 1', 'code' => '1',),
            array( 'sequence' => 12, 'name' => 'Verse 2', 'code' => '2',),
            array( 'sequence' => 13, 'name' => 'Verse 3', 'code' => '3',),
            array( 'sequence' => 14, 'name' => 'Verse 4', 'code' => '4',),
            array( 'sequence' => 15, 'name' => 'Verse 5', 'code' => '5',),
            array( 'sequence' => 16, 'name' => 'Verse 6', 'code' => '6',),
            array( 'sequence' => 17, 'name' => 'Verse 7', 'code' => '7',),
            array( 'sequence' => 20, 'name' => 'Ending',  'code' => 'e',),
        );
        foreach ($song_parts as $value) {
            DB::table('song_parts')->insert($value);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('song_parts');
    }
}
