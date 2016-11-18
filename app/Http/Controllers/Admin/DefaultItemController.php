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
    protected $view_idx = 'default_items.index';
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
    public function index(Request $request)
    {
        $heading = 'Manage Default Event Items';
        $this_type = 'all';

        // eager loading of related table
        $default_items = DefaultItem::with('type')
            ->orderBy('type_id')
            ->orderBy('seq_no');

        if (   $request->has('filterby' ) 
            && $request->has('filtervalue') 
            && $request->filterby=='type'   ) 
        {
            $default_items->where('type_id', $request->filtervalue );
            $this_type = Type::find($request->filtervalue);
            $heading = "Default Event Items for ".$this_type->name;
        }

        // get list of Event Types
        $types = Type::get();

        return view( 
            $this->view_all, 
            array(
                'heading' => $heading,
                'default_items' => $default_items->get(),
                'types' => $types,
                'this_type' => $this_type
                )
            );
    }





    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // get list of possible service types
        $types = Type::all();

        // Get full list of default items
        $default_items = DefaultItem::with('type')
            ->orderBy('type_id')
            ->orderBy('seq_no');

        // show form
        return view( $this->view_one, array('types' => $types, 'default_items' => $default_items->get()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDefaultItemRequest $request)
    {
        // create new item, but omit field file_id if not set
        if (! $request->file_id)
            $new = DefaultItem::create( $request->except('file_id') );
        else 
            $new = DefaultItem::create( $request->all() );

        // special treatment for boolean value
        if (isset($request->forLeadersEyesOnly) && $request->forLeadersEyesOnly=='on')
            $new->forLeadersEyesOnly = True;
        else 
            $new->forLeadersEyesOnly = False;
        $new->save();

        checkDefaultItemsSequencing($new->type_id);

        $status = 'New DefaultItem added.';

        return \Redirect::route($this->view_idx, ['filterby'=>'type', 'filtervalue'=>$new->type_id])
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
    public function edit(Request $request, $id)
    {
        // find a single resource by ID
        $output = DefaultItem::find($id);
        if ($output) {
            // get list of possible service types
            $types = Type::all();            

            // Get full list of default items
            $default_items = DefaultItem::with('type')
                ->orderBy('type_id')
                ->orderBy('seq_no');

            return view( $this->view_one, array('default_item' => $output, 'types' => $types, 'default_items' => $default_items->get() ) );
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

        if (   $request->input('text') === $output->text 
            && $request->input('seq_no') === $output->seq_no 
            && $request->input('type_id') === $output->type_id 
            && $request->input('forLeadersEyesOnly') === $output->forLeadersEyesOnly 
            && ($request->input('file_id') === $output->file_id || $request->input('file_id') == '' )) 
        {
            return \Redirect::route($this->view_idx)
                        ->with(['status' => 'no change']);
        }

        // ignore empty file_id
        $ignore = ['_method', '_token', 'file_category_id', 'file'];
        if ($request->input('file_id')=='')
            array_push( $ignore, 'file_id');

        // get the DefaultItem and update it
        $new = DefaultItem::find($id);
        $new->update( $request->except($ignore) );

        // special treatment for boolean value
        $new->forLeadersEyesOnly = $request->forLeadersEyesOnly;
        $new->save();

        checkDefaultItemsSequencing($new->type_id);

        $message = 'DefaultItem with id "' . $id . '" updated';
        return \Redirect::route($this->view_idx, ['filterby'=>'type', 'filtervalue'=>$output->type_id])
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
            return \Redirect::route($this->view_idx, ['filterby'=>'type', 'filtervalue'=>$output->type_id])
                            ->with(['status' => $message]);
        }
        //
        $message = 'Error! DefaultItem with ID "' . $id . '" not found';
        return \Redirect::route($this->view_idx)
                        ->with(['status' => $message]);
    }
}
