<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Role;


class UserController extends Controller
{


    /**
     * define view names
     */
    protected $view_all     = 'admin.users';
    protected $view_all_idx = 'admin.users.index';
    protected $view_one     = 'admin.user';

    /**
     * Authentication
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:editor', ['except' => ['index', 'show']]);
        $this->middleware('role:administrator', ['only' => ['destroy', 'update']]);
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
        return view( $this->view_all, array('users' => $users, 'heading' => $heading) );
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
        return view( $this->view_one, array('roles' => $roles ) );
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
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
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

        return \Redirect::route($this->view_all_idx)
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
        // show only active users
        // TODO: this currently produces duplicates...
        if ($id=='active')
        {
            $user1 = Role::find(4)->users()->get();
            $users = Role::find(5)->users()->get();
            foreach ($user1 as $value) {
                $users->prepend($value);
            }
            $heading = 'Show Active Users';
            return view( $this->view_all, array('users' => $users, 'heading' => $heading) );
        }

        $message = 'Sorry, show single user not (yet) implemented.';
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
        $output = User::find($id);
        if ($output) {
            // list of possible user roles
            $roles = Role::all();
            return view( 'admin.user', array('user' => $output, 'roles' => $roles ) );
        }
        //
        $message = 'User with id "' . $id . '" not found';
        return \Redirect::route($this->view_all_idx)
                        ->withError(['status' => $message]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
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
                    $message .= '. Admin rights cannot be removed from user with id 1!';
                }
            }
        }

        // update name and email addr
        $user->first_name = $request->input('first_name');
        $user->last_name  = $request->input('last_name');
        $user->email      = $request->input('email');
        $user->save();

        return \Redirect::route($this->view_all_idx)
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
        return \Redirect::route($this->view_all_idx)
                        ->with(['status' => $message]);
    }



}
