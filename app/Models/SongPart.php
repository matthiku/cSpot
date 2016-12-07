<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SongPart extends Model
{

	// actual table name
	protected $table = 'song_parts';

    // fillable field
    protected $fillable = ['name', 'sequence', 'code'];


    public function onsongs() {
    	return $this->hasMany('App\Models\OnSong');
    }

}
