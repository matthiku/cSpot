<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Snap\BibleBooks\BibleBooks;



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




    protected function getBible()
    {
    	return new BibleBooks();
	}

    // return number of chapters per book
    public function getChaptersAttribute()
    {
    	// get the name of the book
    	$book = $this->name;
    	// in the array, the name for the Book of Psalms is 'Psalm'
    	if ($book=='Psalms')
    		$book = 'Psalm';
    	// in the array, the name for the Book of Psalms is 'Psalm'
    	if ($book=='Song of Songs')
    		$book = 'Song of Solomon';

        return $this->getBible()->getNumberOfChapters($book);
    }


}
