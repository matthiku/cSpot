<?php

namespace App\Http\Controllers\Cspot;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Carbon\Carbon;
use Cache;
use Log;

use DOMDocument;
use DOMXPath;
use StdClass;

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



    protected function getWebsite($url, $query=null)
    {
        $token = env('BIBLES_ORG_API_TOKEN');
        if (!$token) return;

        // Set up cURL
        $ch = curl_init();
        // Set the URL
        curl_setopt($ch, CURLOPT_URL, $url.$query);
        // don't verify SSL certificate
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // Return the contents of the response as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // Follow redirects
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        // Set up authentication
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "$token:X");

        // Execute the request
        $response = json_decode( curl_exec($ch) );
        curl_close($ch);

        // save passages in cache with an expiration date
        $expiresAt = Carbon::now()->addDays( env('BIBLE_PASSAGES_EXPIRATION_DAYS', 15) );
        Cache::put( $query, $response, $expiresAt );

        Log::info('retrieving bible passage from remote and saving to cache: '.$query);

        return $response;
    }



    protected function getBibleHubText( $url, $book, $chapter )
    {
        // Set up cURL
        $ch = curl_init();
        // Set the URL
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $html = curl_exec($ch);
        curl_close($ch);

        // create a new object to return         
        $p = [];
        $p[0] = new StdClass;
        $p[0]->copyright = '';
        $p[0]->text = '';
        $p[0]->display = $book.' '.$chapter;
        $p[0]->version_abbreviation = 'NIV';
        $result = new StdClass;
        $result->passages = $p;
        $search = new StdClass;
        $search->result = $result;
        $response = new StdClass;
        $response->search = $search;
        $rr = new StdClass;
        $rr->response = $response;

        # Create a DOM parser object
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        $btext = $dom->getElementById('leftbox');
        foreach ($btext->getElementsByTagName('div') as $ch) {
            if ($ch->getAttribute('class')=='chap') {
                $p[0]->text = $ch->ownerDocument->saveHTML($ch);
            }
            if ($ch->getAttribute('class')=='padbot') {
                $p[0]->copyright = $ch->ownerDocument->saveHTML($ch);
            }
            if ($ch->getAttribute('class')=='vheading') {
                $p[0]->version_abbreviation = $ch->firstChild->data;
            }
        }

        // save passages in cache with an expiration date
        $expiresAt = Carbon::now()->addDays( env('BIBLE_PASSAGES_EXPIRATION_DAYS', 15) );
        Cache::put( $url, $rr, $expiresAt );

        Log::info('retrieving bible passage from remote and saving to cache: '.$url);

        return $rr;
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
     * Get bible text (whole passages or single verses) via API from bibles.org
     */
    public function getBibleText($version, $book, $chapter, $verseFrom, $verseTo)
    {
        // only certain versions are accessible via the API
        $versions = array( 'NASB', 'ESV', 'MSG', 'AMP', 'CEVUK', 'KJVA');

        if ( in_array($version, $versions) ) {
            // create the url and query string
            $url   = "https://bibles.org/v2/passages.js?q[]=";
            $query = "$book+$chapter:$verseFrom-$verseTo&version=eng-$version";

            // restrieve the passage from the cache, if it exists, otherwise rquest it again
            if ( Cache::has( $query ) ) {
                $result = Cache::get( $query );
            } else {
                $result = $this->getWebsite($url, $query);
            }

            if ($result) {
                return response()->json( $result );
            }                
        } 

        // needs to be correct of biblehub.com
        if ($book=='Psalm') $book = 'Psalms';
        // Try to get other versions via BLB 
        $url  = 'http://biblehub.com/'.strtolower($version).'/'.strtolower($book).'/'.$chapter.'.htm';

        if (Cache::has($url)) {
            $result = Cache::get($url);
        } else {
            $result = $this->getBibleHubText( $url, $book, $chapter );
        }

        if ($result) {
            return response()->json( $result );
        }                

        return response()->json("requested failed, no bible text fetched!", 404);
    }



}

