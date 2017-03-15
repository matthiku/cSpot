<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bible extends Model
{
    // mass assignment protection
    protected $fillable = ['bibleversion_id', 'biblebook_id', 'chapter', 'verse', 'text'];


    // timestamps not needed
    public $timestamps = false;



    public function bibleversion() 
    {
        return $this->belongsTo('App\Models\Bibleversion');
    }

    public function biblebook() 
    {
        return $this->belongsTo('App\Models\Biblebook');
    }

}
