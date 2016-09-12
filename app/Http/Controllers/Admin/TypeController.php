<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\StoreTypeRequest;
use App\Http\Controllers\Controller;

use App\Models\Type;
use App\Models\User;
use App\Models\Resource;


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

        $heading = 'Manage list with Types of Services or Events';
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

            // get the resources table
            $resources = Resource::get();

            // also get the users table
            $users = User::orderBy('first_name')->get();

            // return the view with all the data
            return view( 'admin.type', ['type' => $output, 'users' => $users , 'resources' => $resources ] );
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
        // find a single resource by ID
        $output = Type::find($id);
        if ($output) {

            // set default leader to NULL if value was not set
            $output->update(['leader_id' => $request->leader_id=='null' ? null : $request->leader_id]);

            // set default resource to NULL if value was not set
            $output->update(['resource_id' => $request->resource_id=='null' ? null : $request->resource_id]);

            // update the other fields
            $output->update( $request->except([ '_method', '_token', 'leader_id', 'resource_id' ]) );

            // feedback to user and return view with list of types
            $message = 'Type with id "' . $id . '" updated';
            return \Redirect::route('admin.types.index')
                            ->with(['status' => $message]);
        }

        $message = 'Error! Type with id "' . $id . '" not found';
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
            $plans = $output->plans()->get();
            if ( count($plans) ) {
                flashError('Type "' . $output->name . '" is still referred by Plans and cannot be deleted.');
                return redirect()->back();
            }
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
