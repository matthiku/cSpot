<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    //


    protected $fillable = ['title', 'title_2', 'lyrics', 'song_no', 'book_ref', 'author', 'sequence', 'youtube_id', 'link'];


    public function items() 
    {
        return $this->hasMany('App\Models\Item');
    }
    
}
