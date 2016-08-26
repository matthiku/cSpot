<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon\Carbon;

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
     * Relationship with the files table
     */
    public function files() 
    {
        return $this->hasMany('App\Models\File');
    }



    /**
     * A song has a corresponding song text, divided into song parts
     * 
     * this text contains both lyrics with chords interspersed (like the OnSong format)
     */
    public function song_texts() 
    {
        return $this->hasMany('App\Models\Song_text');
    }




    /**
     * Get collection of plans using this song - excluding future plans
     *      (use this method to count the number of times this song was used!)
     */
    public function plansUsingThisSong() {

        $id = $this->id;

        // get list of plans using this song
        $plans = Plan::whereHas('items', function ($query) use ($id) {
            $query->where('song_id', $id) 
                  ->where('date', '<', Carbon::now());
        })->orderBy('date', 'desc')->get();

        return $plans;
    }


    /**
     * Get last date of plans this song was being used
     */
    public function lastPlanUsingThisSong() {

        $id = $this->id;

        // get list of plans using this song
        $plan = Plan::whereHas('items', function ($query) use ($id) {
            $query->where('song_id', $id) 
                  ->where('date', '<', Carbon::now());
        })->orderBy('date', 'desc')->first();

        return $plan;
    }



    /**
     * Only users with certain roles are allowed to see song lyrics of non-PD songs
     *
     * Musicians, Leaders and users with higher roles are normally part of the local church
     * and therefore covered by the church's CCLI MRL license.
     */
    public function getLyricsAttribute( $value )
    {
        // return full value for all PD songs and for users >= Leaders
        if ($this->license=='PD' || Auth::user()->isMusician() ) {
            return $value;
        }

        // For unauthorized users, return only part of the lyrics and a note
        return substr($value, 0, 100).'...(copyrighted material)';

    }


    /**
     * Only users with a leader role are allowed to see song lyrics of non-PD songs
     *
     * Leaders (and users with higher roles) are normally part of the local church
     * and therefore covered by the church's CCLI MRL license.
     */
    public function getChordsAttribute( $value )
    {
        // return full value for all PD songs and for users >= Leaders
        if ($this->license=='PD' || Auth::user()->isMusician() ) {
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
        $equalPos = strpos($value, '=');

        if ( substr($value,0,4)=='http'  &&  $equalPos !== FALSE ) {
            $value = substr( $value, $equalPos+1 );
        }

        $this->attributes['youtube_id'] = $value;
    }





    /**
     * Take the hymnald.net link and just use the last 2 sections
     *
     * typical link: https://www.hymnal.net/en/hymn/h/137
     * We only need to keep the 'c/137'
     */
    public function setHymnaldotnetIdAttribute( $value )
    {
        // this field should be in the format 'x/nnn', wherby x is either 'c', 'h', 'nt' or 'ns'
        $link = explode('/', $value);
        if (isset($link[6])) {
            $value = $link[5].'/'.$link[6];
        }
        $this->attributes['hymnaldotnet_id'] = $value;
    }

    /**
     * Replace the hymnaldotnet_id with a full-fledged link
     */
    public function getHymnaldotnetIdAttribute( $value )
    {
        if (strlen($value)>1  &&  strlen($value)<8)
            // this field should be in the format 'x/nnn', wherby x is either 'c', 'h', 'nt' or 'ns'
            return "https://www.hymnal.net/en/hymn/".$value;
        return $value;
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
