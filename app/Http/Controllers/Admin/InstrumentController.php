<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\StoreInstrumentRequest;
use App\Http\Controllers\Controller;

use App\Models\Instrument;
use App\Models\User;


class InstrumentController extends Controller
{


    /**
     * define view names
     */
    protected $view_all = 'admin.instruments';
    protected $view_idx = 'admin.instruments.index';
    protected $view_one = 'admin.instrument';



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
        $instruments = Instrument::get();

        $heading = 'Manage User Instruments';
        return view( $this->view_all, array('instruments' => $instruments, 'heading' => $heading) );
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
    public function store(StoreInstrumentRequest $request)
    {
        //
        Instrument::create($request->all());
        $status = 'New Instrument added.';
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
        // get all -- USERS -- with this specific instrument id
        $instrument    = Instrument::find($id);
        if ($instrument) {
            $heading = 'User Management - Show '.$instrument->name;
            return view( 'admin.users', array('users' => $instrument->users()->get(), 'heading' => $heading) );
        }
        $message = 'Error! Instrument with ID "' . $id . '" not found';
        return \Redirect::route($this->view_idx)
                        ->with(['status' => $message]);
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
        $output = Instrument::find($id);
        if ($output) {
            return view( $this->view_one, array('instrument' => $output ) );
        }
        //
        $message = 'Error! Instrument with id "' . $id . '" not found';
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
    public function update(StoreInstrumentRequest $request, $id)
    {
        // was there any change?
        $output = Instrument::find($id);
        if ($request->input('name') == $output->name) 
        {
            return \Redirect::route($this->view_idx)
                        ->with(['status' => 'no change']);
        }
        // get this Instrument
        Instrument::where('id', $id)
                ->update($request->except(['_method','_token']));

        $message = 'Instrument with id "' . $id . '" updated';
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
        $output = Instrument::find($id);
        if ($output) {
            $output->delete();
            $message = 'Instrument with id "' . $id . '" deleted.';
            return \Redirect::route($this->view_idx)
                            ->with(['status' => $message]);
        }
        //
        $message = 'Error! Instrument with ID "' . $id . '" not found';
        return \Redirect::route($this->view_idx)
                        ->with(['status' => $message]);
    }
}
