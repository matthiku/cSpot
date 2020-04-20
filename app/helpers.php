<?php

# (C) 2016 Matthias Kuhs, Ireland

use App\Models\Item;
use App\Models\Plan;
use App\Models\Type;
use App\Models\PlanCache;
use App\Models\History;
use App\Models\Song;
use App\Models\SongPart;
use App\Models\File;
use App\Models\User;
use App\Models\Team;
use App\Models\DefaultItem;
use App\Models\Bible;
use App\Models\Biblebook;
use App\Models\Bibleversion;

use App\Events\CspotItemUpdated;

use App\Jobs\SendIntMsgNotification;

use App\Http\Controllers\Cspot\BibleController;

use Carbon\Carbon;

use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Cmgmyr\Messenger\Models\Thread;

use Intervention\Image\ImageManager;

// added MKS 2020-04-21
use Illuminate\Support\Str;


/**
 * Set a flash message in the session.
 *
 * @param  string $message
 * @return void
 */
function flash($message)
{
    session()->flash('message', $message);
}


/**
 * Set a flash message in the session.
 *
 * @param  string $message
 * @return void
 */
function unFlash()
{
    session()->flash('message', '');
}


/**
 * Set a flash ERROR message in the session.
 *
 * @param  string $message
 * @return void
 */
function flashError($message)
{
    session()->flash('error', $message);
}




/**
 * Get list (collection) of Admin(s)
 */
function findAdmins( $field='all' )
{
    $users = User::get();
    $admins = [];
    foreach ($users as $user) {
        if ($user->isAdmin()) {
            if ($field=='all')
                array_push($admins, $user);
            else
                array_push($admins, $user[$field]);
        }
    }
    return $admins;
}



/*\ __________________________________________________
 *
 *                      F I L E S
 *  __________________________________________________
\*/


/**
 * Correct sequence of files attached to an item
 */
function correctFileSequence($item)
{
    // get all files attached to this item
    $files = $item->files()->get()->flatten()->sortBy('pivot_seq_no');

    // update the sequence nomber on each file
    $seq = 1;
    foreach ($files as $file) {
        $file->pivot->update(['seq_no' => $seq]);
        $seq += 1;
    }
}
/**
 * Of the files attached to an item, get the highest sequence number
 */
function getLatestSeqNoOfFilesAttachedToItem($item)
{
    // get all files attached to this item
    $files = $item->files()->get();
    $seq = 0;
    foreach ($files as $file) {
        $seq = max($file->pivot->seq_no, $seq);
    }
    return $seq;
}

/**
 * Return sizes readable by humans
 */
function humanFileSize($bytes, $decimals = 2)
{
  $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB'];
  $factor = floor((strlen($bytes) - 1) / 3);

  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) .
      @$size[$factor];
}

/**
 * Is the mime type an image
 */
function is_image($mimeType)
{
    return starts_with($mimeType, 'image/');
}

/**
 * Save uploaded files to the designated folder
 * and create thumbnail and mini version as well
 */
function saveUploadedFile($request)
{
    $extension = $request->file('file')->getClientOriginalExtension();
    // updated to new Laravel syntax - MKS 2020-04-21
    $token     = str::random(32).'.'.$extension; // new, random, physical file name
    $filename  = $request->file('file')->getClientOriginalName();
    $filesize  = $request->file('file')->getClientSize();
    $maxfilesize = $request->file('file')->getMaxFilesize();

    // check if request contains an id for the file category, otherwise assign 'unset' (0)
    $cat_id = $request->has('file_category_id') ? $request->get('file_category_id') : 0;

    // move the anonymous file to the central location using the random name
    $destinationPath = config('files.uploads.webpath');
    $request->file('file')->move($destinationPath, $token);

    // create a thumbnail copy of a file
    if (in_array(strtolower($extension), ['jpg','gif','png', 'jpeg'])) {
        createThumbs($destinationPath, $token);
    }

    // create and return the new FILE object
    $file = new File([
        'token'    => $token,
        'filename' => $filename,
        'filesize' => $filesize,
        'file_category_id' => $cat_id,
        'maxfilesize' => $maxfilesize,
    ]);
    return $file;
}
/**
  * create a thumbnail copy of a file
  *
  * @param  string  $fPath  file path
  * @param  string  $fName  file name
  *
  * Save small copy of image as thumb_<file name> (max 300 width and max 200 height)
  * Save mini  copy of image as thumb_<file name> (max 150 width)
  *
  * thumbnail should be 300*200, with valid aspect ratio, bottom cropped if needed to retain 300x200!
  * mini version is just 50% of that
  */
function createThumbs($fPath, $fName) {
    // check if file has a valid extension for processing by Intervention/ImageManager
    $ext = pathinfo($fName, PATHINFO_EXTENSION);
    if (! in_array(strtolower($ext), ['jpg','gif','png','jpeg'])) {
        return;
    }
    // resize for thumbnail
    $img = Image::make($fPath.'/'.$fName)
        ->resize(250, null,         // max width
            function ($constraint) {
                $constraint->aspectRatio();
            })
        ->crop(250, 125, 0, 0 );
    $img->save($fPath.'/'.'thumb-'.$fName, 80);
    // resize for mini thumbnail
    $img = $img->resize(125, null,
            function ($constraint) {
                $constraint->aspectRatio();
            });
    $img->save($fPath.'/'.'mini-'.$fName);

}
function deleteThumbs($fPath, $fName) {
    if (file_exists($fPath.'/'.'thumb-'.$fName)) {
        unlink($fPath.'/'.'thumb-'.$fName);
    }
    if (file_exists($fPath.'/'.'mini-'.$fName)) {
        unlink($fPath.'/'.'mini-'.$fName);
    }
}

/**
 * temporary job...
 */
function createThumbsForAll()
{
    chdir( 'public/'.config('files.uploads.webpath') );
    // create list of current files in images folder
    // (exclude thumb_... or mini_...)
    $files = glob('*.*');

    // loop through each file
    foreach ($files as $key => $imgfile) {
        // is it already minified?
        $prefix = explode('-', $imgfile);
        if ( $prefix[0]=='mini' || $prefix[0]=='thumb' ) {
            continue;
        }
        // check if thumb_... or mini_... already exists for this file
        if (file_exists('thumb-'.$imgfile) && file_exists('mini-'.$imgfile) ) {
            continue;
        }
        // create thumb and mini copy of this file
        Log::info("Creating thumbs for $imgfile:\n");
        createThumbs('.', $imgfile);
    }
    // make sure user 'www-data' has access rights to all files
    $files = glob('*.*');
    foreach ($files as $key => $value) {
        chmod($value, 0777);
    }
}



/*\ __________________________________________________
 *
 *                  S O N G S
 *  __________________________________________________
\*/



/**
 * Search for songs; return a maximum of 10
 *
 * @param  string $search
 * @return collection $songs
 */
function songSearch( $search )
{
  // alternatively, using the fulltext index of the lyrics field:
  $result = Song::whereRaw("MATCH(`lyrics`) AGAINST('$search' IN BOOLEAN MODE)");
  if ($result->count()) {
    return $result;
  }
  // alternatively, use the regular search:
  $search = '%'.$search.'%';
  return Song::where('title', 'like', $search)->
             orWhere('title_2', 'like', $search)->
             orWhere('ccli_no', 'like', $search)->
             orWhere('book_ref','like', $search)->
             orWhere('author',  'like', $search)->
             orWhereRaw("match (lyrics) AGAINST ('$search' in boolean mode)");
}

/**
 * Create array of songs with a book reference for easy selection
 *
 * @return JSON array
 */
function MPsongList()
{
    // TODO:
    //   1.) add new field containing only the numeric value of the book_ref field
    //   2.) inject the song stats into each song or do this with an extra AJAX request
    // removed: where('book_ref', 'like', 'MP%') as we do the filtering in the view

    $mp_songs = Song::get(['id','title','book_ref', 'title_2', 'youtube_id']);
    foreach ($mp_songs as $song) {
        # convert book reference to real numbers (e.g. "MP123" to "123")
        $song->number = filter_var($song->book_ref, FILTER_SANITIZE_NUMBER_INT);
    }
    // now order by the new field and remove one hierarchy in the object levels
    $mp_songs = $mp_songs->sortBy('number')->flatten(1);

    return $mp_songs;
}


/**
 * Get date of the last update to the list of songs
 */
function getLastSongUpdated_at()
{
    // during the installation phase, there is no songs....
    // (Use DB instead of model -> cSpot issue #194, item 2)    
    $song = DB::table('songs')->latest('updated_at')->first();
    if ($song)
        return $song->updated_at;
    return null;
}

/**
 * Divide an uploaded OnSong file into OnSong parts
 *
 * @param  string $filepath
 * @return collection $songs
 *
 */
function readOnSongFile($path)
{
    $parts = collect();
}




/*\ __________________________________________________
 *
 *                  I T E M S
 *  __________________________________________________
\*/




/**
 * Go to next or previous item in the list of items of a plan
 *      or swap between chords and sheetmusic
 */
function nextItem($plan_id, $item_id, $direction, $request=null)
{
    // get the full plan
    $plan    = Plan::find($plan_id);

    // items list sort order depending on direction
    $orderBy = "asc";
    if ($direction=='previous')
        $orderBy = 'desc';

    // Get list of items for this plan, full or without FLEO items
    if ( Auth::user()->ownsPlan($plan_id)  && $request && !$request->is('*/present') ) {
        // get all the items for this plan
        $items = $plan->items()
            ->orderBy('seq_no', $orderBy)
            ->get();
    } else {
        // get all items but not "FLEO" items (for leaders eyes only)
        $items = $plan->items()
            ->where('forLeadersEyesOnly', false)
            ->orderBy('seq_no', $orderBy)
            ->get();
    }

    // if no items are presentable, return the id of the first item
    // (cSpot issue #194, item 3)
    if (!$items->count()) return $plan->items()->first()->id;

    // what is the highest seq_no in this plan?
    if ($orderBy=='asc')
        $last_seq_no = $items->last()->seq_no;
    else
        $last_seq_no = $items->first()->seq_no;

    // perhaps the item_id is a seq no
    $Ar_seq_no = explode('-', $item_id);

    // if item_id actually is a seq_no, then the format is: "offline-<plan_id>-<seq_no>"
    if ( count($Ar_seq_no)==3 ) {
        // find item with the seq no
        // third element is the actual seq no
        $curItem = Item::where([
            [ 'plan_id', $plan_id      ],
            [ 'seq_no',  $Ar_seq_no[2] ]
        ])->first();
        if (! $curItem->count())
            return 0;
    }

    // we only received the current item id
    else {
        $curItem = Item::find($item_id);
    }

    // get seq_no of desired next or previous item
    $new_seq_no = 0;    // to prevent unnassigned exception
    if ($direction == 'next') {
        if ($curItem->seq_no == $last_seq_no)  // Are we already at the last item?
            $new_seq_no = 1.0;                  // then we go back to the first item
        else
            $new_seq_no = $curItem->seq_no+1;   // otherwise we select the next item
    }
    elseif ($direction == 'previous') {
        if ($curItem->seq_no == 1.0) {
            $new_seq_no = $last_seq_no;
        } else {
            $new_seq_no = $curItem->seq_no-1;
        }
    }
    elseif ($direction == 'swap') {
        $new_seq_no = $curItem->seq_no;
    }

    // find the new item id
    foreach ($items as $item) {
        if     ($direction == 'next'     && $item->seq_no >= $new_seq_no)
            return $item->id;
        elseif ($direction == 'previous' && $item->seq_no <= $new_seq_no)
            return $item->id;
        elseif ($item->seq_no == $new_seq_no)
            return $item->id;
    }
    return $item->id;
}


function getItemTitle($item, $direction='next')
{
    $nextItemId = nextItem($item->plan_id, $item->id, $direction);
    $item = Item::find($nextItemId);
    if ($item->song_id && $item->song->title) {
        return $item->song->title;
    }
    return $item->comment;
}




/**
 * Insert a new item into the list of items of a plan
 *
 * Make sure the new sequence number fits sequentially into
 *    the list of sequence numbers of the existing items for a plan
 *    and that all current sequence numbers are in 1.0 steps
 *
 * @param object $request
 */
function insertItem( $request )
{
    // get plan id from the hidden input field in the form
    $plan_id    = $request->plan_id;

    // get new seq no for this item
    $new_seq_no = $request->seq_no;

    // get the Plan model and find the plan
    $plan       = Plan::find($plan_id);

    // get all the items for this plan, ordered by their seq_no
    $items      = $plan->items()->orderBy('seq_no')->get();

    // We are going to number all the items of this plan, starting with 1.0
    $counter = 1.0;

    // if the new item already has a seq_no of 1 or smaller, we change it to one
    //    and increase the counter, so that all subsequent items have the correct seq_no

    if ($new_seq_no <= $counter) {
        $new_seq_no = 1;
        $counter = 2;
    }

    // Loop through each item of the plan, making sure the
    //      seq_no of each item is always 1.0 bigger than the previous

    foreach ($items as $item) {

        // Is this the position (seq_no) for the NEW ITEM?
        if ( $new_seq_no <= $item->seq_no  &&  $new_seq_no > $counter-1 ) {

            $new_seq_no = $counter;
            $counter   += 1;
        }

        // If we inserted the new item earlier, all subsequent items
        //    need to have a new seq_no
        if ( $item->seq_no <> $counter ) {

            # update the current loop-item to correspond to the counter
            $item->seq_no = $counter;

            # Now get  the actual DB record
            $i = Item::find($item->id);

            // update the item accordingly
            $i->seq_no = $counter; $i->save();
        }

        // increase the counter to reflect the current seq_no
        $counter += 1.0;
    }

    // change new seq_no if it's bigger than the current counter
    if ($new_seq_no >= $counter-1) {
        $new_seq_no  = $counter;
    }

    // create a new Item using the input data from the request
    $newItem         = new Item( $request->except(['seq_no', 'moreItems', '_token']) );
    $newItem->seq_no = $new_seq_no;

    // check if a song id was provided in the request
    if ( isset($request->song_id) && $request->song_id > 0 ) {
        $newItem->song_freshness = calculateSongFreshness( $request->song_id, $plan->leader_id, $plan->date );
        $newItem->song_id = $request->song_id;
    }
    else
        $newItem->song_id = NULL;   // make sure we do not reference a song!

    // saving the new Item via the relationship to the Plan
    $item = $plan->items()->save( $newItem );

    $plan->new_seq_no     = $new_seq_no;
    $plan->newest_item_id = $item->id;


    // notify event listener that an item was updated
    $new_item = Item::find($plan->newest_item_id);
    if ($new_item)
        event( new CspotItemUpdated($new_item));


    // handle file uplaods
    if ($request->hasFile('file')) {
        flash('Image file added!');
        if ($request->file('file')->isValid()) {
            // use the helper function
            $file = saveUploadedFile($request);
            // add the file as a relationship to the song
            $item->files()->save($file);
        }
        else {
            flash('Uploaded file could not be validated!');
        }
    }

    // handle file linking
    if ($request->has('file_id') && $request->file_id) {
        // get the requested file as object
        $file = File::find($request->file_id);
        // and save it to the plan item
        if ($file) {
            $item->files()->save($file);

            // set item comment to filename
            if ($item->comment==null || $item->comment==false || $item->comment=='' || $item->comment==' ' );
                $item->update(['comment' => $file->filename]);

            flash('Image file was added to the plan');
        }
    }


    if( isset($newItem->song_id) ) {
        $msg = $newItem->song->title;
    } else {
        $msg = $newItem->comment;
    }
    flash('New Item added: ' . $msg);
    return $plan;
}


/**
 * Calculate Song Freshness (0...100%)
 *
 * ... based on the
 * - amount of times this song was used in general
 * - amount of times this song was used by this leader
 * - time span since the song was last used
 */
function calculateSongFreshness($song_id, $leader_id, $planDate)
{
    $song = Song::find($song_id);

    if (!$song) return null;

    // SFI is only for songs
    if ($song->title_2=='video' || $song->title_2=='slides' )
        return null;

    $used_by_all    = $song->plansUsingThisSong()->count();

    $used_by_leader = $song->leadersUsingThisSong($leader_id)->count();

    $last_time_used = $song->lastTimeUsed;


    $daysLapsed = 0;

    if ($last_time_used != null)
        $daysLapsed = $last_time_used->diffInDays($planDate);
    else
        $daysLapsed = 100;    // if song was never used

    $a = 100 - $used_by_all;
    $b = 100 - $used_by_leader * 2;
    $c = 0 + min(100, $daysLapsed);
    $result = ( $a + $b + $c ) / 3;

    Log::debug("Freshness calc.: song_id $song_id, usage: $used_by_all, by leader: $used_by_leader, last time: $daysLapsed - ($a + $b + $c) / 3 = $result");

    return $result;
}



/**
 * ??
 */
function moveItem($id, $direction)
{
    // this item should be moved
    $moveItem = Item::find($id);
    if (!$moveItem) { return false ;}
    // 'move' the item by changing the seq no
    $cur_seq_no = $moveItem->seq_no;
    if ($direction == 'earlier') {$cur_seq_no -= 1.1;}
    if ($direction == 'later'  ) {$cur_seq_no += 1.1;}
    $moveItem->update(['seq_no' => $cur_seq_no]);

     // get all items of the related plan
    $plan  = Plan::find( $moveItem->plan_id );
    $items = $plan->items()->orderBy('seq_no')->get();

    // start the numbering of all items with 1.0
    $counter = 1.0;
    foreach ($items as $item) {
        if ($item->seq_no <> $counter) {
            $i = Item::find($item->id);   # get the actual DB record
            $item->seq_no = $counter;     # update the current selection
            $i->seq_no = $counter;        # update the seq_no
            $i->save();                   # save the record
        }
        $counter += 1.0;
    }
    return true;
}




/**
 * Delete an item from the list of items of a plan
 *
 * (the model migth allow soft deletes!)
 *
 * Make sure the new sequence number fits sequentially into
 *    the list of sequence numbers of the existing items for a plan
 *    and that all current sequence numbers are in 1.0 steps
 *
 * @param object $items
 * @param number $new_seq_no
 */
function deleteItem($id)
{
    // this item should be deleted
    $moveItem = Item::find($id);
    if (!$moveItem) { return false ;}

     // get all items of the related plan
    $plan  = Plan::find( $moveItem->plan_id );
    $items = $plan->items()->orderBy('seq_no')->get();

    // numbering them countering with 1.0
    $counter = 1.0;
    foreach ($items as $item) {
        if ($item->id == $id) {
            $moveItem->delete();
        } else {
            if ($item->seq_no <> $counter) {
                $i = Item::find($item->id); # get the actual DB record
                # if found, update the current selection
                if ($i) {
                    $item->seq_no = $counter;
                    $i->seq_no = $counter;      # update the seq_no
                    $i->save();                 # save the record
                }
            }
            $counter += 1.0;
        }
    }
    return $moveItem;
}



/**
 * RESTORE an item
 *
 * (the model migth allow soft deletes!)
 *
 * Make sure the new sequence number fits sequentially into
 *    the list of sequence numbers of the existing items for a plan
 *    and that all current sequence numbers are in 1.0 steps
 *
 * @param object $items
 * @param number $new_seq_no
 */
function restoreItem($id)
{
    // this item should be restored
    $item    = Item::onlyTrashed()->find($id);
    if (!$item) { return false ;}

    $item->restore();

     // get all items of the related plan
    $plan  = Plan::find( $item->plan_id );
    $items = $plan->items()->orderBy('seq_no')->get();

    // numbering them countering with 1.0
    $counter = 1.0;
    foreach ($items as $item) {
        if ($item->seq_no <> $counter) {
            $i = Item::find($item->id); # get the actual DB record
            $item->seq_no = $counter;   # update the current selection
            $i->seq_no = $counter;      # update the seq_no
            $i->save();                 # save the record
        }
        $counter += 1.0;
    }
    return true;
}



/**
 * check if string contains bible references,
 * returns the bible texts as an array
 *
 * @param string $refString
 * @return array bible text from bibles.org
 */
function getBibleTexts($refString, $local=false)
{
    // regex pattern to match bible references (http://stackoverflow.com/questions/22254746/bible-verse-regex)
    $pattern = '/(\d*)\s*([a-z]+)\s*(\d+)(?::(\d+))?(\s*-\s*(\d+)(?:\s*([a-z]+)\s*(\d+))?(?::(\d+))?)?/i';
    $bibleTexts = [];
    $bb = new BibleController;
    $books = json_decode( $bb->books()->getContent() );

    if ( preg_match($pattern, $refString) ) {

        // several refs are seperated by semicolon
        $refs = explode(';', $refString);

        foreach ($refs as $ref) {
            $parts   = explode('(', $ref );
            if (count($parts)<2) continue;
            $version = explode(')', $parts[1] );
            $bref    = preg_split( '/[\s,:-]+/', $parts[0] );
            $num = 0;
            // check if the first word really is the name of a bible book
            foreach ($bref as $key => $br) {
                if ( ( $br=='1' || $br=='2' || $br=='3' ) && in_array($br.' '.$bref[$key+1], $books) ) {
                    break;
                }
                if ( ! in_array( ucfirst($br), $books)) {
                    $num += 1;
                } else { break; }
            }
            // No correct book name found?
            if (sizeof($bref)-$num < 3 ) continue;

            // book name can be preceeded by a 1,2 or 3
            if ($bref[$num]==1 || $bref[$num]==2 || $bref[$num]==3) {
                $book    = $bref[$num] . ' ' . $bref[1+$num];
                $num++;
            } else {
                $book    = $bref[$num];
            }
            $chapter = $bref[$num+1];
            $verseFr = $bref[$num+2];
            $verseTo = '';
            if ( isset($bref[$num+3]) ) $verseTo = $bref[$num+3];
            if ( $verseTo == '' || ! is_numeric($verseTo) ) {
                $verseTo = $verseFr;
            }

            // if bible version is stored locally, we can get it directly from our DB
            if ($local) {
                // first check version availability
                $bvers = Bibleversion::where('name', $version[0])->first();

                // get the Book as Eloquent object
                //      while avoiding conflicts like "Psalms" vs. "Psalm" or "Song of Songs" vs "Song of Solomon"
                if (strlen($book)>4)
                    $bbook = Biblebook::where('name', 'like', substr($book,0,5).'%')->first();
                else
                    $bbook = Biblebook::where('name', $book)->first();

                // get the actual collection of verses
                if ($bvers && $bbook) {
                    $verses  = Bible::with(['bibleversion', 'biblebook'])
                                    ->where('bibleversion_id', $bvers->id)
                                    ->where('biblebook_id', $bbook->id)
                                    ->where('chapter',      $chapter)
                                    ->where('verse', '>=', $verseFr)
                                    ->where('verse', '<=', $verseTo)
                                    ->get();
                    if ($verses->count())
                        $bibleTexts[] = $verses;
                }
            }

            else {
                // execute the WWW text search
                $text = $bb->getBibleText($version[0], $book, $chapter, $verseFr, $verseTo);

                // was the search successful? then it should contain at least one passage array
                try {
                    $result = json_decode( $text->getContent())->response->search->result->passages ;
                    if ( count($result) > 0 ) {
                        $text = $result[0];
                        $text->text = str_ireplace( 'h3', 'strong', $text->text );
                        $text->text = str_ireplace( 'h2', 'i', $text->text );
                        $bibleTexts[] = $text;
                    }
                }
                catch(Exception $e) { echo 'failed to convert '.$text->__toString(); }
            }
        }
    }
    return $bibleTexts;
}


function deleteCachedItemsContainingSongId(Song $song)
{
    // get all items that refer this song
    $items = $song->items()->get();

    $item_id_list = [];
    // get each item id into an array
    foreach ($items as $item) {
        # find item in cache
        array_push($item_id_list, $item->id);
    }

    // get all items in the cache with the same item_id's
    $cachedItems = PlanCache::whereIn('item_id', $item_id_list);
    $cachedItems->delete();
}


function deleteCachedItemsFromPastPlans($plan_id)
{
    $caches = PlanCache::get();

    // check each cache item if it is outdated
    foreach ($caches as $cache) {
        if ( $cache->plan->date < Carbon::now()
          && $cache->plan->id != $plan_id ) {
            $cache->delete();
        }
    }
}


/** __________________________________________________
 *
 *                  P L A N S
 *  __________________________________________________
 */




/**
 * getUsersRolesAndInstruments
 *
 * Create an array of all users, their roles and instruments
 *
 * @return arary
 */
function getUsersRolesAndInstruments()
{

    // produce array with users and all their roles
    $rou = User::with('roles', 'instruments')->get();
    $userRoles = [];
    foreach ($rou as $user) {
        $roles = [];
        $instruments = [];
        foreach ($user->roles as $value) {
            if ($value->id > 3) { // no administrative roles needed
                array_push($roles, ['role_id'=>$value->id, 'name'=>$value->name] ); }
        }
        // provide data about user's music instruments
        foreach ($user->instruments as $value) {
            array_push($instruments, ['instrument_id'=>$value->id, 'name'=>$value->name] );
        }
        $userRoles[$user->id] = ['name'=>$user->name, 'roles'=>$roles, 'instruments'=>$instruments];
    }
    return $userRoles;
}



/**
 * Create a List of Service Plans
 *    filtered by user (leader/teacher) or by plan type and/or ordered by certain fields
 *
 * @param  filterby     (user|type|date|future)
 *                      Show only plans for a certain user, of a certain type, a certain date or all events
 *                      default is 'user'
 *
 * @param  filtervalue  (user_id|type_id|all)
 *                      default is plans for current user only
 *
 * @param  timeframe    (all|future)
 *                      Show only future plans or all
 *                      default is only future plans
 *
 * @param  orderBy      Field by which the list must be sorted by. Default: Date
 * @param  order        (desc|asc) default is 'asc' = ascending order. Default: Asc
 *
 */
function getPlans($request)
{
    // set default values
    $filterby    = isset($request->filterby)    ? $request->filterby    : 'user';
    $filtervalue = isset($request->filtervalue )? $request->filtervalue : Auth::user()->id;
    $timeframe   = isset($request->timeframe )  ? $request->timeframe   : 'future';
    $orderBy     = isset($request->orderby)     ? $request->orderby     : 'date';
    $order       = isset($request->order)       ? $request->order       : 'asc';

    if ($filtervalue=='[]')
        $filtervalue='all';

    if ($filterby=='type' && $filtervalue=='all') {
        $filterby = 'future';
        $timeframe = 'all';
    }

    if (isset($request->year))
        $timeframe = 'all';


    $plans = [];
    $heading = 'should not appear!';

    // show only plans for certain user ids or all users
    if ($filterby=='user')
    {
        $plans = Plan::with('type');

        // show all plans, past and future?
        if (is_numeric($filtervalue))
            $plans->where( function ($query) use ($filtervalue) {
                    $query
                        ->where('leader_id', $filtervalue)
                        ->orWhere('teacher_id', $filtervalue);
                    });

        if ($timeframe != 'all') {
            $plans->whereDate('date', '>', Carbon::yesterday());
            $heading = 'Upcoming Events for ';
        }
        else
            $heading = 'All Events for ';

        $plans->orderBy($orderBy, $order);

        if ($filtervalue == 'all')
            $heading .= 'all users';
        else
            $heading .= User::find($filtervalue)->first_name;
    }

    // show only plans of certain type
    elseif ($filterby=='type')
    {
        if (is_numeric($filtervalue)) {
            if ($timeframe=='all') {
                $plans = Plan::with('type')
                    ->where('type_id', $filtervalue)
                    ->orderBy($orderBy, $order);
                $heading = 'Show All ';
            } else {
                $plans = Plan::with('type')
                    ->whereDate('date', '>', Carbon::yesterday())
                    ->where('type_id', $filtervalue)
                    ->orderBy($orderBy, $order);
                $heading = 'Show Upcoming ';
            }
            $heading .= Type::find($filtervalue)->name.'s';
        }

        // filtervalue can also be an array of event type id's
        else {
            $filtervalue = json_decode($filtervalue);
            $heading = "Various Upcoming Event Types";

            if ($timeframe=='all') {
                $plans = Plan::with('type')
                    ->whereIn('type_id', $filtervalue)
                    ->orderBy($orderBy, $order);
                $heading = "Various Event Types";
            }
            else
                $plans = Plan::with('type')
                    ->whereDate('date', '>', Carbon::yesterday())
                    ->whereIn('type_id', $filtervalue)
                    ->orderBy($orderBy, $order);
        }
    }

    // show all future plans
    elseif ($filterby=='future')
    {
        // get ALL future plans incl today
        $heading = 'Upcoming Services';
        $plans = Plan::with(['type', 'leader', 'teacher'])
            ->whereDate('date', '>', Carbon::yesterday())
            ->orderBy($orderBy, $order);

        // for an API call, return the raw data in json format (without pagination!)
        if (isset($request->api)) {
            return json_encode($plans->get());
        }
    }

    elseif ($filterby=='date')
    {
        // list only plans of a certain date
        $plans = Plan::with(['type', 'leader', 'teacher'])
            ->whereDate('date', 'like', '%'.$filtervalue.'%')
            ->orderBy($orderBy, $order);
        $heading = 'Events for '.Carbon::parse($filtervalue)->formatLocalized('%A, %d %B %Y');
    }

    return [$plans, $heading];
}


/**
 * get list of future plans where the current user is listed as member
 */
function listOfPlansForUser()
{
    $list = [];
    $team = Team::where('user_id', Auth::user()->id)->get();
    foreach ($team as $key => $value) {
        if ($team[$key]->available) {
            $list[$team[$key]->plan_id] = True;
        }
    }
    return $list;
}



function addDefaultRolesAndResourcesToPlan($plan)
{
    // add leader/teacher roles to the team for this plan
    $team = $plan->teams()->create([
        'user_id' => $plan->leader_id,
        'role_id' => env('LEADER_ID', 5)  // default is 4 if not set in .env
    ]);

    if ($plan->teacher_id) {
        $plan->teams()->create([
            'user_id' => $plan->teacher_id,
            'role_id' => env('TEACHER_ID', 4)   // default is 5 if not set in .env
        ]);
    }

    // add default resource to this plan
    $default_res  = $plan->type->default_resource;

    if ($default_res)
        $resource = $plan->resources()->attach($default_res->id);
}



/**
 * Trigger certain actions when leader or teacher of a plan was changed
 *
 * a) send notification to each invovled
 * b) change the team accordingly
 *
 * @param Request $request all data from the HTTP request (the new data)
 * @param EloquentModel $plan  (the old data)
 */
function checkIfLeaderOrTeacherWasChanged($request, $plan)
{
    $msg = false;

    // check if LEADER was changed
    if ( $plan->leader_id != $request->leader_id ) {

        // check if reason was given for the change
        if (! $request->has('reasonForChange')) {
            flashError('Please provide reason for the change of leader!');
            return false;
        }

        // find the corresponding team record for the leader
        $leader = Team::where([
            ['plan_id', $plan->id],
            ['role_id', env('LEADER_ID', 4)]  // default is 4 if not set in .env
        ]);

        if ($leader->count()) {               // update the team record
            $leader->update(['user_id' => $request->leader_id ]); }
        else {                                // create a new team member...
            $plan->leader_id = $request->leader_id;
            addDefaultRolesAndResourcesToPlan($plan);
        }

        // affected users must be notified of this change accordingly
        $new_leader = User::find($request->leader_id);
        $recipient = $new_leader->id;
        $subject = 'Leader changed for Event on '.Carbon::parse($plan->date)->format('l, jS \\of F Y');
        $msg = Auth::user()->name.' changed the leader for this '.$plan->type->name.' from '.$plan->leader->name.' to '.$new_leader->name;
    }


    // check if TEACHER was changed
    if ( $plan->teacher_id != $request->teacher_id ) {

        // check if a reason was given for the change
        if (! $request->has('reasonForChange')) {
            flashError('Please provide reason for the change of teacher!');
            return false;
        }

        // find the corresponding team record for the teacher
        $teacher = Team::where([
            ['plan_id', $plan->id],
            ['role_id', env('TEACHER_ID', 5)]  // default is 5 if not set in .env
        ]);

        if ($teacher->count()) {               // update the team record
            $teacher->update(['user_id' => $request->teacher_id ]); }
        else {                                // create a new team member...
            $plan->teacher_id = $request->teacher_id;
            addDefaultRolesAndResourcesToPlan($plan);
        }

        // affected users must be notified of this change accordingly
        $new_teacher = User::find($request->teacher_id);
        $recipient = $new_teacher->id;
        $subject = 'Teacher changed for Event on '.Carbon::parse($plan->date)->format('l, jS \\of F Y');
        $msg = Auth::user()->name.' changed the teacher for this '.$plan->type->name.' from '.$plan->teacher->name.' to '.$new_teacher->name;
    }


    if ($msg) {

        // send internal notification and message
        sendInternalMessage( $subject, $msg, $recipient );

        Log::info( $subject . ' - ' . $msg );

        // also create a history record for this change
        $history = new History([
            'user_id'=>Auth::user()->id,
            'changes'=> $subject . "\n" . $msg,
            'reason'=>$request->reasonForChange
        ]);
        $plan->histories()->save($history);
    }

    return true;
}





/** __________________________________________________
 *
 *              M E S S A G E S
 *  __________________________________________________
 */




/**
 * send message via internal messenger
 * @param string $subject
 * @param string $message
 * @param string $recipient user_id
 * @param bool $email send email or not
 */
function sendInternalMessage($subject, $message, $recipient_id, $email=true)
{

    $thread = Thread::create(
        [
            'subject' => $subject,
        ]
    );

    // Message
    $message = Message::create(
        [
            'thread_id' => $thread->id,
            'user_id'   => Auth::user()->id,
            'body'      => $message,
        ]
    );

    // Sender
    Participant::create(
        [
            'thread_id' => $thread->id,
            'user_id'   => Auth::user()->id,
            'last_read' => new Carbon,
        ]
    );

    // Add Recipients
    $thread->addParticipant([$recipient_id]);

    if ($email)
        sendEmailNotification($message);

    return $thread->id;

}
/**
 * Delete the internal message if the associated task was completed
 */
function deleteConfirmRequestThread($id)
{
    $thread = Thread::find($id);
    if ($thread) {
        $thread->delete();
    }
}

/**
 * Send Email notification of new internal messages
 *
 * @param Message $message
 */
function sendEmailNotification(Message $message)
{
    Log::debug('starting dispatch to SendIntMsgNotification');

    dispatch(New SendIntMsgNotification($message));
    return;

    $subject = 'c-SPOT internal message notification';
    $thread = Thread::find($message->thread_id);
    $thread_subject = $thread->subject;
    $message_body = $message->body;

    foreach ($thread->participants as $key => $recipient) {
        $user = $recipient->user;
        # check if user actually wants to be notified
        if ($user->notify_by_email) {

            Mail::send('cspot.emails.notification',
                ['user'=>$user, 'subject'=>$subject, 'messi'=>$message],
                function ($msg) use ($user, $subject) {
                    $msg->from(findAdmins()[0]->email, 'c-SPOT Admin');
                    $msg->to($user->email, $user->fullName);
                    $msg->subject($subject);
                }
            );
        }
    }
}




/** __________________________________________________
 *
 *              V A R I O U S
 *  __________________________________________________
 */


/**
 * Create list of Song Parts indexed by their codes
 */
function getSongPartsByCode()
{
    $parts = SongPart::get();
    $songparts = [];
    foreach ($parts as $part) {
        $songparts[$part->code] = $part;
    }
    return $songparts;
}

/**
 * get list of Songparts  that have not been used yet in this song
 * (to avoid duplicates)
 */
function getRemainingSongParts($song)
{
    $used = [];
    foreach ($song->onsongs as $onsong) {
        array_push($used, $onsong->song_part_id);
    }
    $songparts = App\Models\SongPart::whereNotIn('id', $used)->orderby('sequence')->get();
    return $songparts;
}




/**
 * get current Main Presenter for Presentation Synchronisation
 */
function getMainPresenter()
{
    // set default values
    $mainPresenter['id'] = 0;
    $mainPresenter['name'] = 'none';

    // Do we already have a Main Presenter?
    if (Cache::has('MainPresenter'))
    {
        $mainPresenter = Cache::get('MainPresenter');
    }

    return $mainPresenter;
}




/**
 * Authentication
 *
 * we must allow individual teachers or leaders to modify items of their own plans!
 */
function checkRights($plan) {

    if ( auth()->user()->isEditor() // editor and higher can always
      || auth()->user()->id == $plan->teacher_id
      || auth()->user()->id == $plan->leader_id  )
         return true;

    flash('Only the leader or teacher or editors can modify this plan.');
    return false;
}




/**
 * Get next Plan Date
 *
 * Calculates the next date for an event according to the default values of the event type
 *
 * @param object $plan
 * @return date $nextDate
 */
function getNextPlanDate( $plan )
{
    // get repeat value for this plan type
    $interval = $plan->type->repeat;

    // get default weekday for this plan type
    $weekday = $plan->type->weekday;

    // date of previous plan
    $planDate = $plan->date;

    return newPlanDate($planDate, $interval, $weekday);
}

/**
 * Calculate new date based on interval and Weekday
 */
function newPlanDate( $planDate, $interval, $weekday)
{
    // send default values for another adding amount of days dpending on interval value
    //if ($interval == 'weekly') -> this is the default...
    $newDate =  $planDate->copy()->addDays(7);
    // $newDate;

    // valid intervals are: daily,weekly,biweekly,fortnightly,monthly,quarterly,half-yearly,yearly
    if ($interval == 'daily')
        $newDate->addDays(-6);

    if ($interval == 'biweekly' || $interval == 'fortnightly')
        $newDate->addDays(14);

    if ($interval == 'monthly') {
        $newDate->addWeeks(4);
        // sometimes we need to add 5 weeks ...
        if ($newDate->month == $planDate->month)
            $newDate->addWeek();
    }

    if ($interval == 'quarterly')
        $newDate->addMonths(3);

    if ($interval == 'half-yearly')
        $newDate->addWeeks(26);

    if ($interval == 'yearly')
        $newDate->addWeeks(52);

    return $newDate;
}


/**
 * Define default values for a new Plan of a certain type
 */
function getTypeBasedPlanData($type)
{
    // get repeat value for this plan type
    $interval = $type->repeat;

    // get default weekday for this plan type
    $weekday = $type->weekday;

    // set proposed date according to default weekday for this event type
    $planDate = Carbon::now();
    $weekdayNames = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'];
    if ($weekday!==null && $weekday>=0 && $weekday<7)
        $planDate = new Carbon( 'last '.$weekdayNames[$weekday] );

    // do not propose to create a new event on a day where this event alredy exists
    $plan = Plan::where('type_id', $type->id)->where('date', '>=', Carbon::today())->orderby('date', 'DESC')->first();
    if ($plan)
        $planDate = $plan->date;

    return newPlanDate($planDate, $interval, $weekday);
}



/**
 * Check for duplicates and non-integer sequence number
 * in the list of default items for a specific event type
 * @param int $type_id
 */
function checkDefaultItemsSequencing($type_id)
{
    $items = DefaultItem::where('type_id', $type_id)
        ->orderBy('seq_no')->get();

    foreach ($items as $key => $item) {
        # use the key as the new sequence number
        $item->seq_no = $key + 1;
        $item->save();
    }

}
