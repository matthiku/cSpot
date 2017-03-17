<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Snap\BibleBooks\BibleBooks;



class Bibleversion extends Model
{
    // only opne field to fill out...
 	protected $fillable = ['name', 'copyright'];


 	// timestamps not needed
    public $timestamps = false;


    public function bibles()
    {
        return $this->hasMany('App\Models\Bible');
    }




    protected function getBible()
    {
    	return new BibleBooks();
	}

    // return array of bible book names
    public function getBooksAttribute()
    {
        return $this->getBible()->getArrayOfBooks();
    }



}
