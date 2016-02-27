<?php

namespace App\Http\Controllers\Cspot;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use Snap\BibleBooks\BibleBooks;


class BibleController extends Controller
{


    protected function getBible()
    {
    	$bibleBooks = new BibleBooks();
    	return $bibleBooks;
	}





    /**
      * get list (array) of all books
      *
      * @return array books
      */
    public function books()
    {
        return response()->json( $this->getBible()->getArrayOfBooks() );
    }



    // get number of chapters in a book
    public function chapters($book)
    {
        return response()->json( $this->getBible()->getNumberOfChapters($book) );
    }




    // get number of chapters in ALL books
    public function allChapters()
    {
        $books = $this->getBible()->getArrayOfBooks();

        $chapters = [];
        foreach ($books as $book) {
            $chapters[$book] = $this->getBible()->getNumberOfChapters($book);
        }         

        return response()->json( $chapters );
    }




    // get number of verses in a chapters of a book
    public function verses($book, $chapter)
    {
        return response()->json( $this->getBible()->getNumberOfVerses($book, $chapter) );
    }




    // get number of verses of ALL chapters in ALL books
    public function allVerses()
    {
        $books = $this->getBible()->getArrayOfBooks();

        $chapters = [];
        foreach ($books as $book) {
            $bookChapters = $this->getBible()->getNumberOfChapters($book);
            $verses = [];
            for ($i=1; $i <= $bookChapters ; $i++) { 
                # code...
                $verses[$i] = $this->getBible()->getNumberOfVerses($book, $i);
            }
            $chapters[$book] = $verses;
        }

        return response()->json( $chapters );
    }



    protected function getWebsite($url)
    {
        $token = env('BIBLES_ORG_API_TOKEN');
        if (!$token) return;

        // Set up cURL
        $ch = curl_init();
        // Set the URL
        curl_setopt($ch, CURLOPT_URL, $url);
        // don't verify SSL certificate
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // Return the contents of the response as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // Follow redirects
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        // Set up authentication
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "$token:X");

        // Do the request
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response);        
    }


    /**
     * Get bible text (whole chapters) via API from bibles.org
     */
    public function getChapter($version, $book, $chapter)
    {
        // only certain versions are accessible via the API
        $versions = array( 'NASB', 'ESV', 'MSG', 'AMP', 'CEVUK', 'KJVA');
        if ( ! in_array($version, $versions) ) {
            $version = "ESV";
        } 

        $url = "https://bibles.org/v2/chapters/eng-$version:$book.$chapter.js";

        return response()->json( $this->getWebsite($url) );

    }

    /**
     * Get bible text (whole chapters) via API from bibles.org
     */
    public function getBibleText($version, $book, $chapter, $verseFrom, $verseTo)
    {
        // only certain versions are accessible via the API
        $versions = array( 'NASB', 'ESV', 'MSG', 'AMP', 'CEVUK', 'KJVA');
        if ( ! in_array($version, $versions) ) {
            $version = "ESV";
        } 

        $url = "https://bibles.org/v2/passages.js?q[]=$book+$chapter:$verseFrom-$verseTo&version=eng-$version";
        // "https://bibles.org/v2/passages.js?q[]=acts+1:12-15&version=eng-esv"

        return response()->json( $this->getWebsite($url) );

    }



}

