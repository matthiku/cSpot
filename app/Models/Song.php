<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;
use Auth;


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
     * Get collection of plans using this song
     * (use this to count the number of times this song was used)
     */
    public function plansUsingThisSong() {

        $id = $this->id;

        // get list of plans using this song
        $plans = Plan::whereHas('items', function ($query) use ($id) {
            $query->where('song_id', $id);
        })->orderBy('id', 'desc')->get();

        return $plans;
    }


    /**
     * Get last date of plans this song was being used
     */
    public function lastPlanUsingThisSong() {

        $id = $this->id;

        // get list of plans using this song
        $plan = Plan::whereHas('items', function ($query) use ($id) {
            $query->where('song_id', $id);
        })->orderBy('id', 'desc')->first();

        return $plan;
    }



    /**
     * Only users with a leader role are allowed to see song lyrics of non-PD songs
     *
     * Leaders (and users with higher roles) are normally part of the local church
     * and therefore covered by the church's CCLI MRL license.
     */
    public function getLyricsAttribute( $value )
    {
        // return full value for all PD songs and for users >= Leaders
        if ($this->license=='PD' || Auth::user()->isLeader() ) {
            return $value;
        }

        // return only part of the lyrics and a note
        return substr($value, 0, 100).'...(copyrighted material)';

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
