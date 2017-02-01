<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Item extends Model
{
    
    use SoftDeletes;


    // mass assignment protection
    protected $fillable = [
        'plan_id',
        'song_id',
        'seq_no',
        'comment',
        'show_comment',
        'key',
        'forLeadersEyesOnly',
        'reported_at',
        'hideTitle',
        'song_freshness',
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $dates = [
        'reported_at', 'created_at', 'updated_at'
    ];


    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['plan'];



    /**
     * determine which type of item we have
     * (song, slide, image, scripture or comment)
     * (slide = slide or video)
     */
    public function itemType()
    {
        if ($this->song_id)
            if ($this->song->title_2=='slides' || $this->song->title_2=='video' )
                return $this->song->title_2;
            return 'song';
        return 'other';
    }


    public function plan() 
    {
        return $this->belongsTo('App\Models\Plan');
    }



    public function song() 
    {
        return $this->belongsTo('App\Models\Song');
    }


    /**
     * Relationship with the files table
     */
    public function files() 
    {
        return $this
            ->belongsToMany('App\Models\File')
            ->withPivot('seq_no')
            ->orderBy('seq_no');
    }



    /**
     * Each item can have several (private) notes owned by their creator (user)
     */
    public function itemNotes()
    {
        return $this->hasMany('App\Models\ItemNote');
    }

}
