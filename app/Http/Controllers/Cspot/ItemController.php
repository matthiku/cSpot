<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Http\Controllers\Cspot;

use Illuminate\Http\Request;

use Snap\BibleBooks\BibleBooks;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use App\Models\Song;
use App\Models\Plan;
use App\Models\Item;
use App\Models\File;

use DB;
use Auth;
use Log;



class ItemController extends Controller
{



    /**
     * Authentication
     *
     * we must allow individual teachers or leaders to modify items of their own plans!
     */
    private function checkRights($plan) {

        if ( auth()->user()->isEditor() // editor and higher can always
          || auth()->user()->id == $plan->teacher_id 
          || auth()->user()->id == $plan->leader_id  ) 
             return true;

        flash('Only the leader or teacher or editors can modify this plan.');
        return false;
    }





    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $plan_id, $seq_no)
    {
        // get the plan to which we want to add an item
        $plan = Plan::find( $plan_id );

        // check user rights (teachers and leaders can edit items of their own plan)
        if (! $this->checkRights($plan)) {
            return redirect()->back();
        }

        // get array of possible bible versions
        $versionsEnum = json_decode(env('BIBLE_VERSIONS'));
        // get array of bible books
        $bibleBooks = new BibleBooks();


        $beforeItem = null;
        // check if this is an insertion of an item BEFORE another item
        if ($request->is('*/before/*')) {
            // here, $seq_no actually is the id of the item before which we want to insert a new item
            $beforeItem = Item::find( $seq_no );
            // Make sure we always insert the new item right BEOFRE the current item
            $seq_no = ($beforeItem->seq_no) - 0.1;
            Log::info( 'CREATE-Showing form to create new item to be inserted before '.$beforeItem->seq_no.' '.$beforeItem->id.' - '.$beforeItem->comment );
        }

        // show the form
        return view( 'cspot.item', [
                'plan'         => $plan, 
                'beforeItem'   => $beforeItem, 
                'seq_no'       => $seq_no,
                'versionsEnum' => $versionsEnum,
                'bibleBooks'   => $bibleBooks,
                'bibleTexts'   => [],
            ]);
    }





    /**
     * Store a newly created ITEM in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $plan = Plan::find( $request->input('plan_id') );
        // searching for a song?
        if ($request->has('search')) {
            $songs = songSearch( '%'.$request->search.'%' );
            if (!count($songs)) {
                flash('No songs found for '.$request->search);
                return redirect()->back()->withInput();
            }
            // Success! Just one song found! Update the request with the new song_id
            if (count($songs)==1) {
                $request->song_id = $songs[0]->id;
            } else {
                // as we found several songs, return to view as user must select one
                Log::info('STORE-search resulted in several songs - SeqNo:'.$request->seq_no.' BeforeITemId:'.$request->beforeItem_id);
                return view('cspot.item_select_song', [
                    'songs'      => $songs,
                    'plan'       => $plan,
                    'item_id'    => 0,
                    'seq_no'     => $request->seq_no,
                    'moreItems'  => $request->moreItems,
                    'beforeItem_id' => $request->beforeItem_id,
                ]);
            }
        }

        // check user rights (teachers and leaders can edit items of their own plan)
        if (! $this->checkRights($plan)) {
            return redirect()->back();
        }

        // check if the new item contains at least one ofthe following:
        //       Song, Bible reference or Comment
        if ($request->comment=='' && $request->version=='' && !isset($request->song_id) ) {
            flash('item was empty! Please add a comment, select a bible verse or add a song.');
            return redirect()->back();
        }        

        $beforeItem = null;
        if (isset($request->beforeItem_id)) {
            $beforeItem = Item::find($request->beforeItem_id);
            $request->seq_no = ($beforeItem->seq_no) - 0.1;
        }

        $xfi = isset($beforeItem->id) ? $beforeItem->id.' seqNo:'.$beforeItem->seq_no : 'missing!';
        Log::info('STORE-Inserting new item into plan with seqNo '.$request->seq_no.' - befItemId? '.$xfi);

        // review numbering of current items for this plan and insert the new item
        $plan = insertItem( $request );

        // see if user ticked the checkbox to add another item after this one
        if ($request->moreItems == "Y") {

            // get array of possible bible versions
            $versionsEnum = json_decode(env('BIBLE_VERSIONS'));
            // get array of bible books
            $bibleBooks = new BibleBooks();

            // return back to same view
            return view( 'cspot.item', [
                    'plan'         => $plan, 
                    'seq_no'       => $request->seq_no, 
                    'versionsEnum' => $versionsEnum,
                    'moreItems'    => 'Y',
                    'beforeItem'   => $beforeItem,
                    'bibleBooks'   => $bibleBooks,
                    'bibleTexts'   => [],
                ]);
        }

        // all went well and the user sees the result anyway, so no flash message needed:
        unFlash();

        // back to full plan view, but first,
        // (get plan id from the hidden input field in the form)
        return \Redirect::route( 'cspot.plans.edit', $request->input('plan_id') );
    }







    /**
     * Add a song as a new item into a plan (at the end)
     * 
     * this is called from the Songs List View
     */
    public function addSong($plan_id, $song_id )
    {
        // check user rights (teachers and leaders can edit items of their own plan)
        $plan = Plan::find( $plan_id );
        if (! $this->checkRights($plan)) {
            return redirect()->back();
        }

        if ($plan) {
            // get all current items of this plan
            $items = $plan->items;

            // find item with the highest seq.no
            $seq_no = 50; // (temporary solution)               

            // create a new items object add it to this plan
            $item = new Item([
                'seq_no' => $seq_no, 
                'song_id' => $song_id,
            ]);
            $newItem = $plan->items()->save($item);

            return \Redirect::route( 'cspot.plans.edit', $plan_id );
        }

        flash('Error! Plan with ID "' . $plan_id . '" not found! (F:addSong)');
        return \Redirect::route('home');        
    }



    /**
     * Directly insert a song as a new item into a plan
     */
    public function insertSong($plan_id, $seq_no, $song_id, $moreItems=null, $beforeItem_id=null )
    {
        // check user rights (teachers and leaders can edit items of their own plan)
        $plan = Plan::find( $plan_id );
        if (! $this->checkRights($plan)) {
            return redirect()->back();
        }

        $beforeItem = [];
        // find the seq_no ot the item before which I want to insert this new item
        if ( isset($beforeItem_id) ) {
            $beforeItem = Item::find($beforeItem_id);
            $seq_no = ($beforeItem->seq_no) - 0.1;
        }

        // create a new items object add it to this plan
        $item = new Item([
            'seq_no' => $seq_no, 
            'song_id' => $song_id,
        ]);
        $newItem = $plan->items()->save($item);

        // re-number all items 
        $item = moveItem($newItem->id, 'static');

        Log::info('INSERSONG-'.$seq_no.' new Id:'.$newItem->id.' new seqNo:'.$newItem->seq_no.' befItemId:'.$beforeItem_id);

        if ($moreItems=='Y') {

            $versionsEnum = json_decode(env('BIBLE_VERSIONS'));

            // insert another item after the just created item
            $seq_no = $newItem->seq_no + 1;

            // send confirmation to view
            flash('New item No '.$newItem->seq_no.' inserted with song '.$newItem->song->title);

            $bibleBooks = new BibleBooks();
            
            // show the form
            return view( 'cspot.item', [
                    'plan'         => $plan, 
                    'seq_no'       => $seq_no, 
                    'versionsEnum' => $versionsEnum,
                    'beforeItem'   => $beforeItem,
                    'bibleBooks'   => $bibleBooks,
                    'bibleTexts'   => [],
                ]);
        }

        return \Redirect::route( 'cspot.plans.edit', $plan_id );
    }






    /**
     * Display single items of a plan with options to move to the next or previous item on this plan
     *
     * @param  int     $id      item id
     * @param  string  $present (optional) chords (default), sheetmusic or present (for overhead presentations)
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id, $present=null)
    {
        $item = Item::find($id);

        if ($item) {
            // default is to show chords
            if ( ! $present) { $present = 'chords'; }

            return view('cspot.'.$present, [
                    'item'       => $item,          
                    // all items of the plan to which this item belongs
                    'items'      => $item->plan->items->sortBy('seq_no')->all(),         
                    // what kind of item presentation is requested
                    'type'       => $present,       
                    // the bible text if there was any reference in the comment field of the item
                    'bibleTexts' => getBibleTexts($item->comment)
                ]);
        }

        flash('Error! Item with ID "' . $id . '" was not found! (F:show)');
        return \Redirect::route('home');        
    }






    /**
     * show the previous or NEXT item in the list of items related to a plan
     *  or swap between chords and sheetmusic 
     *
     * @param  int     $id      plan id
     * @param  int     $id      item id
     * @param  string  $direction   next|previous|swap
     * @return \Illuminate\Http\Response
     */
    public function next(Request $request, $plan_id, $item_id, $direction, $chords=null)
    {
        // get seq_no of next or previous item from helper function
        $new_item_id = nextItem( $plan_id, $item_id, $direction );

        // call edit with new item id 
        if ($chords==null) {
            return $this->edit( $plan_id, $new_item_id );
        } 
        if ($direction=='swap') {
            if ($chords=='chords') 
                $chords = 'sheetmusic';
            else
                $chords = 'chords';
        }
        if ($chords=='chords') {
            return $this->show( $new_item_id );
        }
        return $this->show( $new_item_id, $chords );
    }





    /**
     * Show the form for editing an existing ITEM.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($plan_id, $id)
    {
        // get current item
        $plan = Plan::find( $plan_id );

        $item = Item::find($id);
        if (!$item) { 
            flash('Error! Item with ID "' . $id . '" not found! (F:edit)');
            return redirect()->back();
        }
        $seq_no = $item->seq_no;

        $versionsEnum = json_decode(env('BIBLE_VERSIONS'));

        // If this is a song, find out the last time it was used, 
        // that is: Find the newest plan containing an item with this song
        $newestUsage = [];
        $usageCount = 0;
        if ($item->song) {
            $plans = Plan::whereHas('items', function ($query) use ($item) {
                $query->where('song_id', $item->song_id);
            })->where('date', '<', $plan->date)->orderBy('date', 'desc');
            $usageCount = count($plans->get());
            $newestUsage = $plans->first();
        } 

        // check if comment contains a bible reference, then get the bible text
        $bibleTexts = getBibleTexts($item->comment);

        // array of books of the bible
        $bibleBooks = new BibleBooks();
        
        // get list of items for this plan, each with a prober 'title'
        $items = $item->plan->items->sortBy('seq_no')->all();

        $songs = []; # send empty song array
        // send the form
        return view( 'cspot.item', [
                'plan'         => $plan, 
                'seq_no'       => $seq_no, 
                'item'         => $item, 
                'items'        => $items, 
                'songs'        => $songs, 
                'versionsEnum' => $versionsEnum,
                'usageCount'   => $usageCount,
                'newestUsage'  => $newestUsage,
                'bibleBooks'   => $bibleBooks,
                'bibleTexts'   => $bibleTexts,
            ]);
    }



    /**
     * Update the specified ITEM in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, $seq_no=null)
    {
        $item    = Item::find($id);
        if (!$item) {
            flash('Error! Item with ID "' . $id . '" not found! (F:update)');
            return \Redirect::back();
        }
        $plan_id = $item->plan_id;
        $plan    = Plan::find( $plan_id );

        // searching for a song?
        // (but not when changing the seq.no)
        if ( $request->has('search') && $seq_no == null ) {
            $songs = songSearch( '%'.$request->search.'%' );
            if (!count($songs)) {
                flash('No songs found for '.$request->search);
                return redirect()->back()->withInput();
            }
            if (count($songs)==1) {
                $request->song_id = $songs[0]->id;
            } else {
                // as we found several songs, user must select one
                return view('cspot.item_select_song', [
                    'songs'     => $songs, 
                    'plan'      => $plan, 
                    'item_id'   => $id,
                    'seq_no'    => $request->seq_no,
                    'moreItems' => 'N',
                ]);
            }
        }

        // check user rights (teachers and leaders can edit items of their own plan)
        if (! $this->checkRights($plan)) {
            return redirect()->back();
        }

        // handle file uplaods
        if ($request->hasFile('file')) {
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

        if ($seq_no==null) {
            // get all item fields from the request
            $fields = $request->except('_token', '_method');
            // if a single song was selected above, add it to the array
            if (isset($songs)) {
                $fields['song_id'] = $songs[0]->id;
            }
            $item->update( $fields );
        } else {
            // only update the sequence number
            $item->seq_no = $seq_no;
            $item->save();
            $newItem = moveItem( $item->id, 'static');
        }

        // back to full plan view 
        return \Redirect::route('cspot.plans.edit', $plan_id);
    }



    /**
     * Directly update an item with a new song
     */
    public function updateSong($plan_id, $item_id, $song_id )
    {
        // check user rights (teachers and leaders can edit items of their own plan)
        $plan = Plan::find( $plan_id );
        if (! $this->checkRights($plan)) {
            return redirect()->back();
        }

        $item = Item::find($item_id);
        $item->update([
            'song_id' => $song_id,
        ]);

        // send confirmation to view
        flash('New item No '.$item->seq_no.' inserted with song '.$item->song->title);

        return \Redirect::route( 'cspot.plans.edit', $plan_id );
    }







    /**
     * MOVE the specified resource up or down in the list of items related to a plan.
     *
     * @param  int     $id
     * @param  string  $direction
     * @return \Illuminate\Http\Response
     */
    public function move($id, $direction)
    {
        // call helper function to do the actual 'move'
        $item = moveItem($id, $direction);
        if ($item) {
            // back to full plan view 
            return \Redirect::back();
        }
        flash('Error! Item with ID "' . $id . '" not found');
        return \Redirect::back();
    }


    /**
     * REMOVE the specified resource from storage.
     *(if the model allows soft-deletes)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trash($id)
    {
        $item    = Item::find($id);
        if ($item) {
            $plan_id = $item->plan_id;
            $plan    = Plan::find( $plan_id );
        } 
        else {
            flash('Error! Item with ID "' . $id . '" not found! (F:trash)' );
            return \Redirect::back();
        }

        // check user rights (teachers and leaders can edit items of their own plan)
        if (! $this->checkRights($plan)) {
            return redirect()->back();
        }

        // get item and delete it
        $item = deleteItem($id);
        if ($item) {
            // back to full plan view 
            //flash('Item deleted.');
            return \Redirect::route('cspot.plans.edit', $plan_id);
        }
        flash('Error! Problem trying to delete item with ID "' . $id . '"');
        return \Redirect::back();
    }




    /**
     * Restore the item (previously soft-deleted)
     *(if the model allows soft-deletes)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        // check user rights (teachers and leaders can edit items of their own plan)
        $item    = Item::onlyTrashed()->find($id);
        $plan_id = $item->plan_id;
        $plan    = Plan::find( $plan_id );
        if (! $this->checkRights($plan)) {
            return redirect()->back();
        }

        // get item and restore it
        $item = restoreItem($id);
        if ($item) {
            // back to full plan view 
            flash('Item restored.');
            return \Redirect::back();
        }
        flash('Error! Item with ID "' . $id . '" not found! (F:restore)');
        return \Redirect::back();
    }



    /**
     * PERMANENTLY DELETE an item 
     *
     */
    public function permDelete( $id )
    {
        $item    = Item::onlyTrashed()->find($id);
        if (!$item) {
            flash('Error! Item with ID "' . $id . '" not found! (F:permDelete)');
            return redirect()->back();
        }

        $item->forceDelete();

        flash('Trashed item with id '.$id.' deleted permanently');
        return \Redirect::back();
    }


    /**
     * PERMANENTLY DELETE all trashed items of a plan
     *
     */
    public function deleteAllTrashed( $plan_id )
    {
        // this item should be restored
        $items = Item::onlyTrashed()->where('plan_id', $plan_id);
        if (!$items) return false;

        $items->forceDelete();

        flash('All trashed items deleted');
        return \Redirect::back();
    }


    /**
     * RESTORE all trashed items of a plan
     *
     */
    public function restoreAllTrashed( $plan_id )
    {
        // this item should be restored
        $items = Item::onlyTrashed()->where('plan_id', $plan_id)->get();
        if (!$items) return false;

        // restore all items and try to restore their correct sequence number
        foreach ($items as $item) {
            restoreItem($item->id);
        }

        flash('All trashed items restored. Please review the sequence!');
        return \Redirect::back();
    }




    /**
     * IMAGES HANDLING
     *
     */
    public function indexFiles(Request $request)
    {
        $files = File::paginate(18);

        $querystringArray = $request->input();

        $heading = 'List All Files and Images';
        // URL contains ...?item_id=xxx (needed in order to add an existing file to an item)
        $item_id = 0;
        if ($request->has('item_id')) {
            $item_id = $request->item_id;
            $heading = 'Select a file for the Plan Item';
        }

        // for pagination, always append the original query string
        $files = $files->appends($querystringArray);

        return view('admin.files', ['files'=>$files, 'item_id'=>$item_id, 'heading'=>$heading]);
    }

    /**
     * Add existing file to a plan item
     */
    public function addFile($item_id, $file_id)
    {
        $item = Item::find($item_id);
        if ($item) {
            $file = File::find($file_id);
            $item->files()->save($file);
            return \Redirect::route( 'cspot.items.edit', [$item->plan_id, $item->id] );
        }
        flash('Error! Item with ID "' . $id . '" not found');
        return \Redirect::back();
    }

    /**
     * Change seq_no of a file 
     */
    public function moveFile($item_id, $file_id, $direction)
    {
        $item = Item::find($item_id);
        if ($item) {
            $file = File::find($file_id);
            if ($file) {
                if ($direction=='up') {
                    $file->seq_no = $file->seq_no-1.1;
                }
                if ($direction=='down') {
                    $file->seq_no = $file->seq_no+1.1;
                }
                $file->save();
                // make sure all files atteched to this item have the correct seq no now
                correctFileSequence($item_id);
                return \Redirect::route( 'cspot.items.edit', [$item->plan_id, $item->id] );
            }
            flash('Error! File with ID "' . $file_id . '" not found');
        } else {
            flash('Error! Item with ID "' . $item_id . '" not found');
        }
        return \Redirect::back();
    }


}

