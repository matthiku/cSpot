<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\SongPart;


class SongPartController extends Controller
{


    /**
     * Authentication
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:editor', ['except' => ['index', 'show']]);
    }


    /**
     * define view names
     */
    protected $view_all = 'admin.song_parts';
    protected $view_idx = 'admin.song_parts.index';
    protected $view_one = 'admin.song_part';




    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // get all items
        $song_parts = SongPart::get();

        return view(
            $this->view_all, 
            [
                'heading'    => 'Manage Song Part Names', 
                'song_parts' => $song_parts
            ]);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // get all items
        $song_parts = SongPart::get();
        
        // return the simple Add/Edit view, but provide a list of existing names
        return view(
            $this->view_one, 
            ['song_parts' => $song_parts]
        );
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // get all items
        $song_parts = SongPart::get();

        // get all existing names as an array
        $part_names = SongPart::pluck('name');

        $message = "Missing name!";

        // check for name value and save new resource if found
        if ($request->has('name')) {

            $name = $request->name;

            if ($part_names->contains($name)) {
                $error = 'Error! Song Parts name "' . $name . '" already exists!';
                $request->session()->flash('error', $error);
                // $message = '';
            } 
            else {
                SongPart::create($request->all());
                $song_parts = SongPart::get();
                return view( $this->view_all, 
                    [
                        'heading'    => 'Manage Song Part Names', 
                        'song_parts' => $song_parts
                    ]);
            }
        }
        return view(
            $this->view_one, 
            ['song_parts' => $song_parts]
        )
        ->with('status', $message);
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
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // get all items
        $song_parts = SongPart::get();
        return view(
            $this->view_one, 
            [
                'song_parts' => $song_parts,
                'song_part' => SongPart::find($id)
            ]
        );
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // get this item
        $sp = SongPart::find($id);

        // get all items
        $song_parts = SongPart::get();

        // get all existing names as an array
        $part_names = SongPart::pluck('name');

        $message = "Missing name!";

        // check for name value and save new resource if found
        if ($request->has('name')) {

            $name = $request->name;

            if ($part_names->contains($name)) {
                $error = 'Error! Song Parts name "' . $name . '" already exists!';
                $request->session()->flash('error', $error);
            } 
            else {
                $sp->update($request->all());
                $song_parts = SongPart::get();
                return view( $this->view_all, 
                    [
                        'heading'    => 'Manage Song Part Names', 
                        'song_parts' => $song_parts
                    ]);
            }
        }
        return view(
            $this->view_one, 
            ['song_parts' => $song_parts]
        )
        ->with('status', $message);

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
    }
}
