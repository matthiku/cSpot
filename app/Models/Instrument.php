<?php

# (C) 2016 Matthias Kuhs, Ireland


namespace App\Models;

use App\Models\User;

use Illuminate\Database\Eloquent\Model;



class Instrument extends Model
{
    //

    protected $fillable = ['name'];



    public function users()
    {
        return $this->belongsToMany('App\Models\User');
    }


}


