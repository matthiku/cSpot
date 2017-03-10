<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Biblebook extends Model
{
    //
 	protected $fillable = ['name'];


    public function bibles()
    {
        return $this->belongsToMany('App\Models\Bible');
    }


}
