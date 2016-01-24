<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\StoreTypeRequest;
use App\Http\Controllers\Controller;

use App\Models\Type;


class TypeController extends Controller
{



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
        //
        $types = Type::get();

        $heading = 'Manage list with Types of Services';
        return view( 'admin.types', array('types' => $types, 'heading' => $heading) );
    }





    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.type');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTypeRequest $request)
    {
        //
        Type::create( $request->except('_token') );
        $status = 'New Type added.';
        return \Redirect::route('admin.types.index')
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
        // get all service plans of this specific type id
        $type    = Type::find($id);
        $heading = 'Show  Service Plans of Type '.$type->name;
        return view( 'cspot.plans', array('plans' => $type->plans()->get(), 'heading' => $heading) );
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
        $output = Type::find($id);
        if ($output) {
            return view( 'admin.type', array('type' => $output ) );
        }
        //
        $message = 'Error! Type with id "' . $id . '" not found';
        return \Redirect::route('admin.types.index')
                        ->with(['status' => $message]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreTypeRequest $request, $id)
    {
        // was there any change?
        $output = Type::find($id);
        if ($request->input('name') == $output->name) {
            return \Redirect::route('admin.types.index')
                        ->with(['status' => 'no change']);
        }
        // get this Type
        Type::where('id', $id)
                ->update($request->except(['_method','_token']));

        $message = 'Type with id "' . $id . '" updated';
        return \Redirect::route('admin.types.index')
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
        $output = Type::find($id);
        if ($output) {
            $output->delete();
            $message = 'Type with id "' . $id . '" deleted.';
            return \Redirect::route('admin.types.index')
                            ->with(['status' => $message]);
        }
        //
        $message = 'Error! Type with ID "' . $id . '" not found';
        return \Redirect::route('admin.types.index')
                        ->with(['status' => $message]);
    }
}
