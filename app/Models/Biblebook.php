<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Biblebook extends Model
{
    //
 	protected $fillable = ['name'];


 	// timestamps not needed
    public $timestamps = false;


    public function bibles()
    {
        return $this->hasMany('App\Models\Bible');
    }


}
