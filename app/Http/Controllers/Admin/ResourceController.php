<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\StoreResourceRequest;
use App\Http\Controllers\Controller;

use App\Models\Resource;
use App\Models\Plan;


class ResourceController extends Controller
{


    /**
     * define view names
     */
    protected $view_all = 'admin.resources';
    protected $view_one = 'admin.resource';
    protected $route_idx = 'resources.index';



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
        $resources = Resource::get();

        $heading = 'Manage User Resources';
        return view( $this->view_all, array('resources' => $resources, 'heading' => $heading) );
    }





    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view($this->view_one);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreResourceRequest $request)
    {
        //
        Resource::create($request->all());
        $status = 'New Resource added.';
        return \Redirect::route($this->route_idx)
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
        // get all -- PLANS -- with this specific resource id
        $resource    = Resource::find($id);
        $heading = 'Plan Management - Show '.$resource->name;
        return view( 'cspot.plans', array('plans' => $resource->users()->get(), 'heading' => $heading) );
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
        $output = Resource::find($id);
        if ($output) {
            return view( $this->view_one, array('resource' => $output ) );
        }
        //
        $message = 'Error! Resource with id "' . $id . '" not found';
        return \Redirect::route($this->route_idx)
                        ->with(['status' => $message]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreResourceRequest $request, $id)
    {
        // default resources cannot be changed
        // was there any change?
        $output = Resource::find($id);

        // get this Resource
        Resource::where('id', $id)
                ->update($request->except(['_method','_token']));

        $message = 'Resource with id "' . $id . '" updated';
        return \Redirect::route($this->route_idx)
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
        $output = Resource::find($id);
        if ($output) {
            $output->delete();
            $message = 'Resource with id "' . $id . '" deleted.';
            return \Redirect::route($this->route_idx)
                            ->with(['status' => $message]);
        }
        //
        $message = 'Error! Resource with ID "' . $id . '" not found';
        return \Redirect::route($this->route_idx)
                        ->with(['status' => $message]);
    }
}
