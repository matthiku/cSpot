<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Controllers\Controller;

use App\Models\Role;
use App\Models\User;


class RoleController extends Controller
{


    /**
     * define view names
     */
    protected $view_all = 'admin.roles';
    protected $view_idx = 'roles.index';
    protected $view_one = 'admin.role';



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
        $roles = Role::get();

        $heading = 'Manage User Roles';
        return view( $this->view_all, array('roles' => $roles, 'heading' => $heading) );
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
    public function store(StoreRoleRequest $request)
    {
        //
        Role::create($request->all());
        $status = 'New Role added.';
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
        // get all -- USERS -- with this specific role id
        $role    = Role::find($id);
        $heading = 'User Management - Show '.$role->name;
        return view( 'admin.users', array('users' => $role->users()->get(), 'heading' => $heading) );
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
        $output = Role::find($id);
        if ($output) {
            return view( $this->view_one, array('role' => $output ) );
        }
        //
        $message = 'Error! Role with id "' . $id . '" not found';
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
    public function update(StoreRoleRequest $request, $id)
    {
        // default roles cannot be changed
        if ($id<4)
        {
            return \Redirect::route($this->view_idx)
                        ->with(['status' => 'System default roles cannot be changed!']);
        }
        // was there any change?
        $output = Role::find($id);
        if ($request->input('name') == $output->name) 
        {
            return \Redirect::route($this->view_idx)
                        ->with(['status' => 'no change']);
        }
        // get this Role
        Role::where('id', $id)
                ->update($request->except(['_method','_token']));

        $message = 'Role with id "' . $id . '" updated';
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
        $output = Role::find($id);
        if ($output) {
            $output->delete();
            $message = 'Role with id "' . $id . '" deleted.';
            return \Redirect::route($this->view_idx)
                            ->with(['status' => $message]);
        }
        //
        $message = 'Error! Role with ID "' . $id . '" not found';
        return \Redirect::route($this->view_idx)
                        ->with(['status' => $message]);
    }
}
