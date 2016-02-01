<?php

# (C) 2016 Matthias Kuhs, Ireland

// see https://tuts.codingo.me/laravel-social-and-email-authentication

namespace App\Models;

use App\Models\User;

use Illuminate\Database\Eloquent\Model;



class Role extends Model
{
    //

    protected $fillable = ['name'];



    public function users()
    {
        return $this->belongsToMany('App\Models\User');
    }


}


