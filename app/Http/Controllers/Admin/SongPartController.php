<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\StoreSongPartRequest;
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
        $song_parts = SongPart::orderby('sequence')->get();

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
        $song_parts = SongPart::orderby('sequence')->get();
        
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
    public function store(StoreSongPartRequest $request)
    {
        // data has alredy been validatet so we can create a new resource
        SongPart::create($request->all());

        return view( $this->view_all, 
            [
                'heading'    => 'Manage Song Part Names', 
                'song_parts' => SongPart::orderby('sequence')->get()
            ]);
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
        return view(
            $this->view_one, 
            [
                'song_parts' => SongPart::orderby('sequence')->get(),
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
    public function update(StoreSongPartRequest $request, $id)
    {
        // get this item
        $sp = SongPart::find($id);

        $sp->update($request->all());

        return view( $this->view_all, 
            [
                'heading'    => 'Manage Song Part Names', 
                'song_parts' => SongPart::orderby('sequence')->get()
            ]);

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
        return "not implemented yet!";
    }
}
