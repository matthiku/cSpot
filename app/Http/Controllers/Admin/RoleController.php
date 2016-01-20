<?php

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
     * Authentication
     */
    public function __construct() {
        $this->middleware('admin');
        $this->middleware('auth');
        $this->middleware('auth', ['except' => ['index', 'show']]);
        $this->middleware('admin', ['except' => ['index', 'show']]);
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
        return view( 'admin.roles', array('roles' => $roles, 'heading' => $heading) );
    }





    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.role');
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
        return \Redirect::route('admin.roles.index')
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
        // get all users with this specific role id
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
            return view( 'admin.role', array('role' => $output ) );
        }
        //
        $message = 'Error! Role with id "' . $id . '" not found';
        return \Redirect::route('admin.roles.index')
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
        //
        // get this Role
        Role::where('id', $id)
                ->update($request->except(['_method','_token']));

        $message = 'Role with id "' . $id . '" updated';
        return \Redirect::route('admin.roles.index')
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
            return \Redirect::route('admin.roles.index')
                            ->with(['status' => $message]);
        }
        //
        $message = 'Error! Role with ID "' . $id . '" not found';
        return \Redirect::route('admin.roles.index')
                        ->with(['status' => $message]);
    }
}
