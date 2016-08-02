<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Role;
use App\Models\Instrument;

use Auth;


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
        $this->middleware('role:editor', ['except' => ['index', 'show', 'update']]);
        $this->middleware('role:administrator', ['only' => ['destroy', 'create']]);
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
        // create list of possible user roles
        $roles = Role::all();
        // create list of instruments
        $instruments = Instrument::all();
        return view( $this->view_one, array('roles' => $roles, 'instruments' => $instruments ) );
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

        // create list of instruments
        $instruments = Instrument::all();
        // which instrument was assigned in the form?
        foreach ($instruments as $instrument) {
            if ($request->has( str_replace(' ','_',$instrument->name)) ) {
                $user->assignInstrument($instrument);
            } 
            else {
                $user->removeInstrument($instrument);
            }
        }

        // get list of possible user roles
        $roles = Role::all();
        // which role was assigned in the form?
        foreach ($roles as $role) {
            if ($request->has( str_replace(' ','_',$role->name)) ) {
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

        // users can only see their own profile, unless they are Admins
        if ( Auth::user()->id<>$id  &&   ! Auth::user()->isAdmin() ) {
            flash('You are not authorized for this request.');
            return redirect()->back();
        }

        // show the user profile
        $heading = "Edit your profile";
        $user = User::find($id);
        $roles = Role::all();
        $instruments = Instrument::all();
        return view($this->view_one, ['user' => $user, 'roles' => $roles, 'heading' => $heading, 'instruments' => $instruments]);
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
            // create list of instruments
            $instruments = Instrument::all();
            return view( 'admin.user', array('user' => $output, 'roles' => $roles, 'instruments' => $instruments ) );
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
        // users can only see their own profile, unless they are Admins
        if ( Auth::user()->id<>$id  &&   ! Auth::user()->isAdmin() ) {
            flash('You are not authorized for this request.');
            return redirect()->back();
        }

        // get the current user record
        $user = User::find($id);

        $alert = '';
        // prepare success message
        $message = 'User with id "' . $id . '" updated';

        // create list of instruments
        $instruments = Instrument::all();
        // which instrument was assigned in the form?
        foreach ($instruments as $instrument) {
            if ($request->has( str_replace(' ','_',$instrument->name)) ) {
                $user->assignInstrument($instrument);
            } 
            else {
                $user->removeInstrument($instrument);
            }
        }

        // get list of possible user roles
        $roles = Role::all();
        // which role was assigned in the form?
        foreach ($roles as $role) {
            if ($request->has( str_replace(' ','_',$role->name) ) ) {
                $user->assignRole($role);
            } 
            else {
                if ($user->id == Auth::user()->id && $role->name<>'administrator' ) {
                    $alert = 'Admin rights cannot be removed from current user! Ask a new Admin to do that.';
                } else {
                    $user->removeRole($role);
                }
            }
        }

        // update name and email addr
        $user->first_name = $request->input('first_name');
        $user->last_name  = $request->input('last_name');
        $user->name  = $request->input('name');

        $user->notify_by_email = $request->notify_by_email;

        // only Admins can change the email address
        if (Auth::user()->isAdmin()) {
            $user->email      = $request->input('email');
        }
        $user->save();

        // send admins back to all users view
        if (Auth::user()->isAdmin()) {
            return \Redirect::route($this->view_all_idx)
                        ->with(['status' => $message])
                        ->with(['error'  => $alert]);
        }
        // send 'normal' users back to profile view
        return redirect()->route('admin.users.show', [$user->id]);
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
