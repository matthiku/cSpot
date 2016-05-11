<?php

# (C) 2016 Matthias Kuhs, Ireland

use App\Models\Item;
use App\Models\Plan;
use App\Models\Song;
use App\Models\File;
use App\Models\User;

use App\Http\Controllers\Cspot\BibleController;


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



/**
 * Return sizes readable by humans
 */
function human_filesize($bytes, $decimals = 2)
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


function saveUploadedFile($request)
{
    $extension = $request->file('file')->getClientOriginalExtension();
    $token     = str_random(32).'.'.$extension;
    $filename  = $request->file('file')->getClientOriginalName();

    // move the anonymous file to the central location
    $destinationPath = config('files.uploads.webpath');
    $request->file('file')->move($destinationPath, $token);

    $file = new File([
        'token'    => $token,
        'filename' => $filename
    ]);
    return $file;  
}



/**
 * Search for songs
 *
 * @param  string $search
 * @return collection $songs
 */
function songSearch( $search )
{
    return Song::where('title', 'like', $search)->
             orWhere('title_2', 'like', $search)->
             orWhere('ccli_no', 'like', $search)->
             orWhere('book_ref','like', $search)->
             orWhere('author',  'like', $search)->
             orWhere('lyrics',  'like', $search)->
             take(10)->get();
}



/**
 * Go to next or previous item in the list of items of a plan
 *      or swap between chords and sheetmusic
 */
function nextItem($plan_id, $item_id, $direction)
{
    $curItem = Item::find($item_id);
    $plan    = Plan::find($plan_id);
    // get all the items for this plan
    $items   = $plan->items()->orderBy('seq_no')->get();

    // get seq_no of desired next or previous item
    $new_seq_no = 0;    // to prevent unnassigned exception
    if ($direction == 'next') {
        if ($curItem->seq_no == count($items)) {
            $new_seq_no = 1.0;
        } else {
            $new_seq_no = $curItem->seq_no+1;
        }
    }
    elseif ($direction == 'previous') {
        if ($curItem->seq_no == 1.0) {
            $new_seq_no = count($items);
        } else {
            $new_seq_no = $curItem->seq_no-1;
        }
    } 
    elseif ($direction == 'swap') {
        $new_seq_no = $curItem->seq_no;
    }

    // find the new item id
    foreach ($items as $item) {
        if ($item->seq_no == $new_seq_no) {
            return $item->id;
        }
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
 * @param object $items
 * @param number $new_seq_no
 */
function insertItem( $request )
{
    // get plan id from the hidden input field in the form
    $plan_id = $request->plan_id;
    // get new seq no for this item
    $new_seq_no = $request->seq_no;
    // get the Plan model and find the plan
    $plan  = Plan::find($plan_id);
    // get all the items for this plan
    $items = $plan->items()->orderBy('seq_no')->get();

    Log::info('INSERTITEM-newSeqNo old:'.$new_seq_no);

    // numbering the items, starting with 1.0
    $counter = 1.0;
    if ($new_seq_no <= $counter) {
        $new_seq_no = 1;
        $counter = 2;
    }
    foreach ($items as $item) {
        if ($new_seq_no <= $item->seq_no && $new_seq_no > $counter-1 ) {
            $new_seq_no = $counter;
            $counter += 1;
        }
        if ($item->seq_no <> $counter) {
            $i = Item::find($item->id); # get the actual DB record
            $item->seq_no = $counter;     # update the current selection
            $i->seq_no = $counter;        # update the seq_no
            $i->save();                 # save the record
        }
        $counter += 1.0;
    }
    // change new seq_no if it's bigger than the current counter
    if ($new_seq_no >= $counter) {
        $new_seq_no  = $counter;
    }
    // create a new Item using the input data from the request
    $newItem = new Item( $request->except(['seq_no', 'moreItems', '_token']) );
    $newItem->seq_no = $new_seq_no;
    // check if a song id was provided in the request
    if (isset($request->song_id)) {
        $newItem->song_id = $request->song_id;
    }
    // saving the new Item via the relationship to the Plan
    $plan->items()->save( $newItem );
    $plan->new_seq_no = $new_seq_no;

    Log::info('INSERTITEM-newSeqNo new:'.$new_seq_no);

    if( isset($newItem->song_id) ) {
        $msg = $newItem->song->title;
    } else {
        $msg = $newItem->comment;
    }
    flash('New Item added: ' . $msg);
    return $plan;
}




/**
 * Delete an item from the list of items of a plan
 *
 * Make sure the new sequence number fits sequentially into 
 *    the list of sequence numbers of the existing items for a plan
 *    and that all current sequence numbers are in 1.0 steps
 *
 * @param object $items
 * @param number $new_seq_no
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
    return true;
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
function getBibleTexts($refString) 
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
                $book    = $bref[$num] . '+' . $bref[1+$num];
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
            // execute the text search
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
    return $bibleTexts;
}
