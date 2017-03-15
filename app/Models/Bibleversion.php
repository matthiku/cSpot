<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bibleversion extends Model
{
    // only opne field to fill out...
 	protected $fillable = ['name'];


 	// timestamps not needed
    public $timestamps = false;


    public function bibles()
    {
        return $this->hasMany('App\Models\Bible');
    }

}
