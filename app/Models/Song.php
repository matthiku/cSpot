<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;


class Song extends Model
{
    // in order to retain DB integrity, songs are only marked as deleted
    // in cases where a song is still referred to by a plan
    use SoftDeletes;

    protected $fillable = ['title', 'title_2', 'lyrics', 'song_no', 'book_ref', 'author', 'sequence', 'youtube_id', 'link', 'license'];



    /**
     * Relationship with the Items table
     */
    public function items() 
    {
        return $this->hasMany('App\Models\Item');
    }




    /**
     * Helper method to get list of license types
     */
    public function getLicenseEnum()
    {
        $versions = explode("','", substr(DB::select("SHOW COLUMNS FROM ".(new \App\Models\Song)->getTable()." LIKE 'license'")[0]->Type, 6, -2));
        return $versions;        
    }


    
}
