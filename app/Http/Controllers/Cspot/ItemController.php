<?php

namespace App\Http\Controllers\Cspot;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\StoreItemRequest;
use App\Http\Controllers\Controller;

use App\Models\Song;
use App\Models\Plan;
use App\Models\Item;

use DB;
use Auth;

class ItemController extends Controller
{



    /**
     * Authentication
     */
    public function __construct() {
        $this->middleware('role:author', ['except' => ['index', 'show']]);
    }




    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        flash('Sorry, this is not (yet) implemented.');
        return redirect()->back();
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($plan_id, $seq_no)
    {
        // get the plan to which we want to add an item
        $plan = Plan::find( $plan_id );
        $t = new Item();
        $versionsEnum = $t->getVersionsEnum();
        return view( 'cspot.item', [
                'plan' => $plan, 
                'seq_no' => $seq_no, 
                'versionsEnum' => $versionsEnum
            ]);
    }


    /**
     * Store a newly created ITEM in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreItemRequest $request)
    {
        // searching for a song?
        if ($request->has('search')) {
            $songs = songSearch( '%'.$request->search.'%' );
            if (!count($songs)) {
                flash('No songs found for '.$request->search);
                return redirect()->back()->withInput();
            }
            // success! just one song found, update the request with the song id
            if (count($songs)==1) {
                $request->song_id = $songs[0]->id;
            } else {
                // as we found several songs, return to view as user must select one
                $request->session()->flash('songs', $songs);
                return redirect()->back();
            }
        }

        // review numbering of current items for this plan and insert the new item
        $plan = insertItem( $request );

        // see if user ticked the checkbox to add another item after this one
        if ($request->moreItems == "Y") {
            // update seq no in the session
            $request->session()->flash( 'new_seq_no', $plan->new_seq_no+0.5 );
            // return back to same view
            return redirect()->back();
        }

        // back to full plan view, but first,
        // (get plan id from the hidden input field in the form)
        return \Redirect::route( 'cspot.plans.edit', $request->input('plan_id') );
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //

        flash('Sorry, this is not implemented.');
        return redirect()->back();
    }



    /**
     * Show the form for editing an ITEM.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($plan_id, $id)
    {
        // get current item
        $plan = Plan::find( $plan_id );
        $item = Item::find($id);
        $seq_no = $item->seq_no;
        $versionsEnum = $item->getVersionsEnum();

        $songs = []; # send empty song array
        // send the form
        return view( 'cspot.item', [
                'plan' => $plan, 
                'seq_no' => $seq_no, 
                'item' => $item, 
                'songs' => $songs, 
                'versionsEnum' => $versionsEnum
            ]);
    }



    /**
     * Update the specified ITEM in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreItemRequest $request, $id)
    {
        $item    = Item::find($id);
        $plan_id = $item->plan_id;
        $plan    = Plan::find( $plan_id );

        // check if user can actually edit this plan
        $user = Auth::user();
        if( $user->isEditor() || $user->id == $plan->leader_id || $user->id == $plan->teacher_id )
        {
            // searching for a song?
            if ($request->has('search')) {
                $songs = songSearch( '%'.$request->search.'%' );
                if (!count($songs)) {
                    flash('No songs found for '.$request->search);
                    return redirect()->back()->withInput();
                }
                if (count($songs)==1) {
                    $request->song_id = $songs[0]->id;
                } else {
                    // as we found several songs, user must select one
                    $request->session()->flash('songs', $songs);
                    return redirect()->back();
                }
            }
            // get current item
            $item->update($request->except('_token'));
        }
        else {
            flash('You are not authorized to edit this item. Please ask a leader or teacher or Admin.');
        }
        // back to full plan view 
        return \Redirect::route('cspot.plans.edit', $plan_id);
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
        // get item and delete it
        $item = moveItem($id, $direction);
        if ($item) {
            // back to full plan view 
            flash('Item moved.');
            return \Redirect::back();
        }
        flash('Error! Item with ID "' . $id . '" not found');
        return \Redirect::back();
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // get item and delete it
        $item = deleteItem($id);
        if ($item) {
            // back to full plan view 
            flash('Item deleted.');
            return \Redirect::back();
        }
        flash('Error! Item with ID "' . $id . '" not found');
        return \Redirect::back();
    }
}
