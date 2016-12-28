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

    protected $appends = [
        'itemsCount',
        'lastTimeUsed',
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
    public function onsongs() 
    {
        return $this->hasMany('App\Models\OnSong');
    }


    /**

     *  Get the full lyrics text with encoded part headers 

     */
    public function onSongLyrics() {

        $lyrics = '';
        $line = '';

        // add each onsong part but prepend it with it's part code as a header
        foreach ($this->onsongs as $onsong) {

            // ignore parts containing music instructions (like 'Capo')
            if ( $onsong->song_part->code != 'm'  &&  $onsong->song_part->code != 'i' ) {
                if (trim($line)!='') 
                    $lyrics .= "\n"; // newline char not on the first line

                $lyrics .= '[' . $onsong->song_part->code . "]\n";

                // remove OnSong codes enclosed in square brackets and split by lines
                $lines = preg_split('/$\R?^/m' , preg_replace("/\[[^\]]+\]/m", '', $onsong->text));

                // now add each line to the lyrics, but not if it's a comment or musical instruction
                $lkey = 0;
                foreach ($lines as $line) {
                    if (substr($line,0,1)!='#'  &&  substr($line,0,1)!='(' ) {
                        if ($lkey > 0) 
                            $lyrics .= "\n"; // newline char not on the first line
                        $lyrics .= $line;
                        $lkey++;
                    } 
                    else
                        $line = '';
                }
                // ignore trailing empty line
                $lyrics = rtrim($lyrics, "\n\n");
            }
        }

        // check if we had any onsong lyrics...
        if ( strlen($lyrics) > 5 )
            return $lyrics;

        return $this->lyrics;

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
     * Get collection of plans of leaders using this song - excluding future plans
     *      (use this method to count the number of times this song was used by a certain leader)
     */
    public function leadersUsingThisSong($leader_id) {

        $id = $this->id;

        // get list of plans using this song
        $plans = Plan::whereHas('items', function ($query) use ($id, $leader_id) {
            $query->where('song_id', $id)
                  ->where('leader_id', $leader_id)
                  ->where('date', '<', Carbon::now());
        })->orderBy('date', 'desc')->get();

        return $plans;
    }

    /**
     * Get collection of plans using this song - INCLUDING future plans
     */
    public function allPlansUsingThisSong() {

        $id = $this->id;

        // get list of plans using this song
        $plans = Plan::whereHas('items', function ($query) use ($id) {
            $query->where('song_id', $id);
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





    /* ---------------- ACCESSORS and MUTATORS ------------------ */


    /**
     * count number of times this song has been used in a plan
     */
    public function getItemsCountAttribute() {
        return $this->items->count();
    }


    /**
     * get date of last time this song was used in a plan
     */
    public function getLastTimeUsedAttribute( $value ) {

        $id = $this->id;

        // get list of plans using this song
        $plan = Plan::whereHas('items', function ($query) use ($id) {
            $query->where('song_id', $id) 
                  ->where('date', '<', Carbon::now());
        })->orderBy('date', 'desc')->first();

        if ($plan)
            return $plan->date;
        return null;
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
     * Check if the CCLI NO contains the full URL -
     * we only want to save the ID part
     *
     * @param  string  $value
     * @return string
     */
    public function setCcliNoAttribute( $value )
    {
        if ($value) {
            $link = explode('/', $value);
            if (isset($link[4]) && is_numeric($link[4])) {
                $value = $link[4];
            }
            $this->attributes['ccli_no'] = $value;
        }
        // special treatment if value is empty -> null
        else 
            $this->attributes['ccli_no'] = null;
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
