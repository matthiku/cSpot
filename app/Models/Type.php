<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    //

    protected $fillable = ['name', 'start_time', 'end_time'];



    public function plans()
    {
        return $this->hasMany('App\Models\Plan', 'type_id');
    }


}
