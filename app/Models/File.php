<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{

    // mass assignment protection
    protected $fillable = [ 'token', 'filename', 'filesize', 'file_category_id' ];


    public $timestamps = false;


    public function song() 
    {
        return $this->belongsTo('App\Models\Song');
    }


    public function item() 
    {
        return $this->belongsTo('App\Models\Item');
    }

    public function items() 
    {
        return $this->belongsToMany('App\Models\Item')->withPivot('seq_no');
    }


    public function defaultItems() 
    {
        return $this->belongsToMany('App\Models\DefaultItem');
    }



    public function file_category() 
    {
        return $this->belongsTo('App\Models\FileCategory');
    }
    
}
