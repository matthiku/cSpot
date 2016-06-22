<?php

# (C) 2016 Matthias Kuhs, Ireland


namespace App\Models;

use App\Models\File;

use Illuminate\Database\Eloquent\Model;



class FileCategory extends Model
{
    //

    protected $fillable = ['name'];



    public function files()
    {
        return $this->hasMany('App\Models\File');
    }


}


