<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Item extends Model
{

    // mass assignment protection
    protected $fillable = [
        'plan_id',
        'song_id',
        'seq_no',
        'comment',
        'version',
        'key',
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];


    public function plan() 
    {
        return $this->belongsTo('App\Models\Plan');
    }



    public function song() 
    {
        return $this->belongsTo('App\Models\Song');
    }

}
