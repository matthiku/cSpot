<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBiblebooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('biblebooks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unique('name');
        });

        // start inserting these static values ...
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 1, 'Genesis']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 2, 'Exodus']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 3, 'Leviticus']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 4, 'Numbers']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 5, 'Deuteronomy']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 6, 'Joshua']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 7, 'Judges']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 8, 'Ruth']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 9, '1 Samuel']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 10, '2 Samuel']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 11, '1 Kings']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 12, '2 Kings']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 13, '1 Chronicles']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 14, '2 Chronicles']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 15, 'Ezra']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 16, 'Nehemiah']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 17, 'Esther']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 18, 'Job']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 19, 'Psalms']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 20, 'Proverbs']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 21, 'Ecclesiastes']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 22, 'Song of Songs']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 23, 'Isaiah']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 24, 'Jeremiah']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 25, 'Lamentations']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 26, 'Ezekiel']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 27, 'Daniel']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 28, 'Hosea']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 29, 'Joel']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 30, 'Amos']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 31, 'Obadiah']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 32, 'Jonah']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 33, 'Micah']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 34, 'Nahum']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 35, 'Habakkuk']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 36, 'Zephaniah']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 37, 'Haggai']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 38, 'Zechariah']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 39, 'Malachi']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 40, 'Matthew']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 41, 'Mark']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 42, 'Luke']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 43, 'John']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 44, 'Acts']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 45, 'Romans']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 46, '1 Corinthians']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 47, '2 Corinthians']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 48, 'Galatians']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 49, 'Ephesians']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 50, 'Philippians']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 51, 'Colossians']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 52, '1 Thessalonians']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 53, '2 Thessalonians']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 54, '1 Timothy']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 55, '2 Timothy']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 56, 'Titus']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 57, 'Philemon']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 58, 'Hebrews']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 59, 'James']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 60, '1 Peter']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 61, '2 Peter']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 62, '1 John']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 63, '2 John']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 64, '3 John']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 65, 'Jude']);
        DB::insert('INSERT INTO `biblebooks` (`id`, `name`) VALUES (?,?)', [ 66, 'Revelation']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('biblebooks');
    }
}
