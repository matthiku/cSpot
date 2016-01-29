<?php

use App\Models\Item;
use App\Models\Plan;
use App\Models\Song;

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
 * Search for songs
 *
 * @param  string $search
 * @return collection $songs
 */
function songSearch( $search )
{
    return Song::where('title', 'like', $search)->
             orWhere('title_2', 'like', $search)->
             orWhere('song_no', 'like', $search)->
             orWhere('book_ref', 'like', $search)->
             orWhere('author', 'like', $search)->
             get();
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
    $plan_id = $request->input('plan_id');
    // get new seq no for this item
    $new_seq_no = $request->input('seq_no');
    // get the Plan model and find the plan
    $plan  = Plan::find($plan_id);
    // get all the items for this plan
    $items = $plan->items()->orderBy('seq_no')->get();
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
            $i = Item::find($item->id); # get the actual DB record
            $item->seq_no = $counter;     # update the current selection
            $i->seq_no = $counter;        # update the seq_no
            $i->save();                 # save the record
        }
        $counter += 1.0;        
    }
    return true;
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
                $item->seq_no = $counter;     # update the current selection
                $i->seq_no = $counter;        # update the seq_no
                $i->save();                 # save the record
            }
            $counter += 1.0;        
        }
    }
    return true;
}