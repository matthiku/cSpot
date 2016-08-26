<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Song_part extends Model
{
    //
    protected $fillable = ['name'];

    /**
     * Relationship with the files table
     */
    public function song_texts() 
    {
        return $this->hasMany('App\Models\Song_text');
    }


}
