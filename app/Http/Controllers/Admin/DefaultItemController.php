<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\StoreDefaultItemRequest;
use App\Http\Controllers\Controller;

use App\Models\DefaultItem;
use App\Models\Type;


class DefaultItemController extends Controller
{


    /**
     * define view names
     */
    protected $view_all = 'admin.default_items';
    protected $view_idx = 'admin.default_items.index';
    protected $view_one = 'admin.default_item';



    /**
     * Authentication
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:editor', ['except' => ['index', 'show']]);
    }




    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // eager loading of related table
        $default_items = DefaultItem::with('type')
            ->orderBy('type_id')
            ->orderBy('seq_no')
            ->get();

        $heading = 'Manage Default Service Items';
        return view( $this->view_all, array('default_items' => $default_items, 'heading' => $heading) );
    }





    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // get list of possible service types
        $types = Type::all();
        // show form
        return view( $this->view_one, array('types' => $types));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDefaultItemRequest $request)
    {
        //
        DefaultItem::create( $request->all() );
        $status = 'New DefaultItem added.';
        return \Redirect::route($this->view_idx)
                        ->with(['status' => $status]);
    }







    /**
     * Display linked records of the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $message = 'Sorry, show single resource not yet implemented.';
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
        // find a single resource by ID
        $output = DefaultItem::find($id);
        if ($output) {
            // get list of possible service types
            $types = Type::all();            
            return view( $this->view_one, array('default_item' => $output, 'types' => $types ) );
        }
        //
        $message = 'Error! DefaultItem with id "' . $id . '" not found';
        return \Redirect::route($this->view_idx)
                        ->with(['status' => $message]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreDefaultItemRequest $request, $id)
    {
        // was there any change?
        $output = DefaultItem::find($id);
        if ($request->input('text') == $output->text && $request->input('seq_no') == $output->seq_no ) 
        {
            return \Redirect::route($this->view_idx)
                        ->with(['status' => 'no change']);
        }
        // get this DefaultItem
        DefaultItem::where('id', $id)
                ->update($request->except(['_method','_token']));

        $message = 'DefaultItem with id "' . $id . '" updated';
        return \Redirect::route($this->view_idx)
                        ->with(['status' => $message]);
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
        // find a single resource by ID
        $output = DefaultItem::find($id);
        if ($output) {
            $output->delete();
            $message = 'DefaultItem with id "' . $id . '" deleted.';
            return \Redirect::route($this->view_idx)
                            ->with(['status' => $message]);
        }
        //
        $message = 'Error! DefaultItem with ID "' . $id . '" not found';
        return \Redirect::route($this->view_idx)
                        ->with(['status' => $message]);
    }
}
