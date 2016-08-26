<?php

/**
 * Model for the table chords, which actually contains 
 * the lyrics (song texts) AND the chords - interspersed like the OnSong format
 * The song text is divided into parts according to verse, chorus and bridge etc.
 */


namespace App;

use Illuminate\Database\Eloquent\Model;

class Song_text extends Model
{


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['song_id', 'part_id', 'text'];




    /**

     * Get the song that owns these lyrics

     */
    public function song() {

        $this->belongsTo('App\Models\Song');

    }



    /**

     * Get the part name for these lyrics

     */
    public function song_part() {

        $this->belongsTo('App\Models\Song_part');

    }




}
