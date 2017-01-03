<?php

/**
 * Model for the Song_Texts table which actually contains 
 * the lyrics (song texts) AND the chords - interspersed like the OnSong format
 * The song text is divided into parts according to verse, chorus and bridge etc.
 */


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnSong extends Model
{

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['song'];



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['song_id', 'song_part_id', 'text'];




    /**

     * Get the song that owns these lyrics

     */
    public function song() {

        return $this->belongsTo('App\Models\Song');

    }



    /**

     * Get the part name for these lyrics

     */
    public function song_part() {

        return $this->belongsTo('App\Models\SongPart');

    }



}
