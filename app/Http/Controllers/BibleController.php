<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use Snap\BibleBooks\BibleBooks;


class BibleController extends Controller
{

    public function getBible()
    {
    	$bibleBooks = new BibleBooks();
    	return $bibleBooks;
	}


    // get list of all books
    public function books()
    {
        return response()->json( $this->getBible()->getArrayOfBooks() );
    }

    // get number of chapters in a book
    public function chapters($book)
    {
        return response()->json( $this->getBible()->getNumberOfChapters($book) );
    }

    // get number of verses in a chapters of a book
    public function verses($book, $chapter)
    {
        return response()->json( $this->getBible()->getNumberOfVerses($book, $chapter) );
    }


}
