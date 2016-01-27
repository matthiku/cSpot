<?php

namespace App\Http\Controllers\Cspot;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\StoreItemRequest;
use App\Http\Controllers\Controller;

use App\Models\Song;
use App\Models\Plan;
use App\Models\Item;

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
        return redirect()->back()->with(['message' => $message]);
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($plan_id, $seq_no)
    {
        // get the plan to which we want to add an item
        $plan = Plan::with('items')->find( $plan_id );
        // get songs table
        $songs = '';//Song::get();
        return view( 'cspot.item', ['songs' => $songs, 'plan' => $plan, 'seq_no' => $seq_no] );
    }


    /**
     * Store a newly created ITEM in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreItemRequest $request)
    {
        // review numbering of current items for this plan and insert the new item
        $plan = insertItem( $request );

        flash('New Item added.');
        // see if user ticked checkbox to add another item after this one
        if ($request->moreItems == "Y") {
            // get songs table
            $songs = '';//Song::get();
            // return back to same view
            return view( 'cspot.item', ['songs' => $songs, 'plan' => $plan, 'seq_no' => $plan->new_seq_no+0.5]);
        }
        // get plan id from the hidden input field in the form
        $plan_id = $request->input('plan_id');
        // back to full plan view 
        return \Redirect::route('cspot.plans.edit', $plan_id);
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
        flash('Sorry, this is not (yet) implemented.');
        return redirect()->back();
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($plan_id, $id)
    {
        // get current item
        $item = Item::find($id);
        $seq_no = $item->seq_no;

        // dummy songs for now
        $songs = '';
        // send the form
        return view( 'cspot.item', ['songs' => $songs, 'plan_id' => $plan_id, 'seq_no' => $seq_no, 'item' => $item] );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // get current item
        $item = Item::find($id);
        $item->update($request->except('_token'));
        $plan_id = $item->plan_id;
        // back to full plan view 
        return \Redirect::route('cspot.plans.edit', $plan_id);
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
