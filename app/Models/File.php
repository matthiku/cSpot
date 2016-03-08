<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{

    // mass assignment protection
    protected $fillable = [ 'token', 'filename' ];


    public $timestamps = false;


    public function song() 
    {
        return $this->belongsTo('App\Models\Song');
    }

    
}
