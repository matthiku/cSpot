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
        $message = 'Sorry, this is not (yet) implemented.';
        return redirect()->back()->with(['message' => $message]);
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($plan_id)
    {
        // get the plan to which we want to add an item
        $plan = Plan::with('items')->find($plan_id);
        // get songs table
        $songs = Song::get();
        return view( 'cspot.item', ['songs' => $songs, 'plan' => $plan] );
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreItemRequest $request)
    {
        $plan_id = $request->input('plan_id');
        // create a new Item using the input data from the request
        $newItem = new Item( $request->except($plan_id) );
        // getting the Plan model
        $plan = Plan::find($plan_id);
        // saving the new Item via the relationship to the Plan
        $plan->items->save($newItem);

        $status = 'New Item added.';
        return \Redirect::back()
                        ->with(['status' => $status, 'plan' => $plan]);
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
        $message = 'Sorry, this is not (yet) implemented.';
        return redirect()->back()->with(['message' => $message]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $message = 'Sorry, this is not (yet) implemented.';
        return redirect()->back()->with(['message' => $message]);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
