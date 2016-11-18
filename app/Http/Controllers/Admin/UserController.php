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
    protected $view_all_idx = 'users.index';
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
    public function index(Request $request)
    {
        // show only active users
        // TODO: this currently produces duplicates...
        if ($request->has('active'))
        {
            $user1 = Role::find(4)->users()->get();
            $users = Role::find(5)->users()->get();
            foreach ($user1 as $value) {
                $users->prepend($value);
            }
            $heading = 'Show Active Users';

            return view( 
                $this->view_all, [
                    'users' => $users,
                    'heading' => $heading,
                    'roles' => Role::get(),
                    'instruments' => Instrument::get()
                ]);
        }

        // get all users in the requested order (default by id)
        $users = User::
            orderBy(
                isset($request->orderby)     ? $request->orderby     : 'id', 
                isset($request->order)       ? $request->order       : 'asc'
            );

        $heading = 'List of all Users';

        // check if user selected a filter
        if ($request->has('filterby') && $request->has('filtervalue') && $request->filtervalue!='all') {

            if ($request->filterby=='role') {
                // get all -- USERS -- with this specific role id
                $role       = Role::find($request->filtervalue);
                $users      = $role->users();
                $heading    = 'All Users with Role "'.ucfirst($role->name).'"';
            } 
            else if ($request->filterby=='instrument') {
                // get all -- USERS -- with this specific instrument id
                $instrument = Instrument::find($request->filtervalue);
                $users      = $instrument->users();
                $heading    = 'All Users Playing '.ucfirst($instrument->name);
            } else {
                $users = $users->where($request->filterby, $request->filtervalue);
            }
        }

        return view( 
            $this->view_all, [
                'users' => $users->get(),
                'heading' => $heading,
                'roles' => Role::get(),
                'instruments' => Instrument::get()
            ]);
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
                if ($user->id == Auth::user()->id && $role->name=='administrator' ) {
                    $alert = 'Admin rights cannot be removed from current user! Ask a new Admin to do that.';
                } else {
                    $user->removeRole($role);
                }
            }
        }

        // update name and email addr
        $user->first_name = $request->input('first_name');
        $user->last_name  = $request->input('last_name');
        $user->name       = $request->input('name');
        $user->startPage  = $request->has('startPage') ? $request->startPage : '';

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
        return redirect()->route('users.show', [$user->id]);
    }




    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);

        // Prevent first user to be deleted
        if ( $id==1 || !$user ) {
            $message = 'User with id "' . $id . '" cannot be deleted!';
        } 
        else {
            //  first remove any roles 
            if ($user->roles->count())
                $user->roles()->detach();

            // check if user has events assigned
            if ($user->plans_as_leader->count()>0 || $user->plans_as_teacher->count()>0) {
                $message = 'User with id "' . $id . '" still has events assigned and cannot be deleted!';
            }
            else {
                $user->delete();
                $message = 'User with id "' . $id . '" was deleted';
            }
        }
        return \Redirect::route($this->view_all_idx)
                        ->with(['status' => $message]);
    }



    public function setStartPage(Request $request, $id)
    {
        // users can only see their own profile, unless they are Admins
        if ( Auth::user()->id<>$id  &&   ! Auth::user()->isAdmin() ) {
            //TODO change this to a JSON response
            return 'You are not authorized for this request.';
        }

        // get the current user record
        $user = User::find($id);

        if ($request->has('url')) {
            // set startPage in user record
            $user->update(['startPage'  => $request->url]);
            return 'OK!';
        }

        return 'missing URL!';

    }


}
