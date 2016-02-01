<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;


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


    public function getVersionsEnum()
    {
        $versions = explode("','", substr(DB::select("SHOW COLUMNS FROM ".(new \App\Models\Item)->getTable()." LIKE 'version'")[0]->Type, 6, -2));
        return $versions;        
    }

}
