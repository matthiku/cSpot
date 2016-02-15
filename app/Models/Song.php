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

    

    protected $fillable = [
        'title',
        'title_2',
        'lyrics',
        'chords',
        'ccli_no',
        'book_ref',
        'author',
        'sequence',
        'youtube_id',
        'link',
        'license',
        'hymnaldotnet_id',
        'chords',
    ];



    /**
     * Relationship with the Items table
     */
    public function items() 
    {
        return $this->hasMany('App\Models\Item');
    }




    /**
     * Check if the YouTube id contains the full URL -
     * we only want to save the ID part
     *
     * @param  string  $value
     * @return string
     */
    public function setYoutubeIdAttribute( $value )
    {
        if ( strpos($value, '=') !== FALSE ) {
            $new_yt_id = explode('=', $value);
            $value = $new_yt_id[1];
        }
        $this->attributes['youtube_id'] = $value;
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
