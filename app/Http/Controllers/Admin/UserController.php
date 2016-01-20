<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\StoreUserRequest;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Role;


class UserController extends Controller
{


    /**
     * Authentication
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('admin');
        $this->middleware('auth', ['except' => ['index', 'show']]);
        $this->middleware('admin', ['except' => ['index', 'show', 'edit', 'update']]);
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $users = User::get();

        $heading = 'User Management';
        return view( 'admin.users', array('users' => $users, 'heading' => $heading) );
    }





    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // list of possible user roles
        $roles = Role::all();
        return view( 'admin.user', array('roles' => $roles ) );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        // get the current user record
        $user = new User;

        // update name and email addr
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->save();

        // prepare success message
        $message = 'User with id "' . $user->id . '" added';

        // get list of possible user roles
        $roles = Role::all();
        // which role was assigned in the form?
        foreach ($roles as $role) {
            if ($request->has($role->name)) {
                $user->assignRole($role);
            } else {
                $user->removeRole($role);
            }
        }

        return \Redirect::route('admin.users.index')
                        ->with(['status' => $message]);
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
        return 'show user not implemented';
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
        $output = User::find($id);
        if ($output) {
            // list of possible user roles
            $roles = Role::all();
            return view( 'admin.user', array('user' => $output, 'roles' => $roles ) );
        }
        //
        $message = 'User with id "' . $id . '" not found';
        return \Redirect::route('admin.users.index')
                        ->withError(['status' => $message]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUserRequest $request, $id)
    {
        // get the current user record
        $user = User::find($id);

        // prepare success message
        $message = 'User with id "' . $id . '" updated';

        // get list of possible user roles
        $roles = Role::all();
        // which role was assigned in the form?
        foreach ($roles as $role) {
            if ($request->has($role->name)) {
                $user->assignRole($role);
            } else {
                if ($user->id<>1 || $role->name<>'administrator' ) {
                    $user->removeRole($role);
                } else {
                    $message .= '. Admin rights cannot be removed from first user!';
                }
            }
        }

        // update name and email addr
        $user->name = $request->input('name');
        $user->email = $request->input('email');

        return \Redirect::route('admin.users.index')
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
        // Prevent first user to be deleted
        if ($id==1) {
            $message = 'User with id "' . $id . '" cannot be deleted!';
        } else {
            $user = User::where('id', $id)->delete();
            $message = 'User with id "' . $id . '" was deleted';
        }
        return \Redirect::route('admin.users.index')
                        ->with(['status' => $message]);
    }



}
