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
        'key',
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];



    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['plan'];



    public function plan() 
    {
        return $this->belongsTo('App\Models\Plan');
    }



    public function song() 
    {
        return $this->belongsTo('App\Models\Song');
    }



}
