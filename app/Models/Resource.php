<?php

# (C) 2016 Matthias Kuhs, Ireland


namespace App\Models;

//use App\Models\Plan;

use Illuminate\Database\Eloquent\Model;



class Resource extends Model
{
    //

    protected $fillable = ['name', 'type', 'details' ];



    public function plans()
    {
        return $this->belongsToMany('App\Models\Plan');
    }


}


