<?php

namespace App\Http\Controllers\Cspot;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Song;


class SongController extends Controller
{

    /**
     * define view names
     */
    protected $view_all = 'cspot.songs';
    protected $view_idx = 'cspot.songs.index';
    protected $view_one = 'cspot.song';



    /**
     * Authentication
     */
    public function __construct() {
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
        $songs = Song::get();

        $heading = 'Manage Songs';
        return view( $this->view_all, array('songs' => $songs, 'heading' => $heading) );
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
    public function store(StoreSongRequest $request)
    {
        //
        Song::create($request->all());
        flash('New Song added.');
        return \Redirect::route($this->view_idx);
    }







    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // get all -- PLANS -- with this specific song id
        $song    = Song::find($id);
        $heading = 'Show Plans using the Song '.$song->name;
        return view( 'cspot.plans.index', array('plans' => $song->plans, 'heading' => $heading) );
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
        $output = Song::find($id);
        if ($output) {
            return view( $this->view_one, array('song' => $output ) );
        }
        //
        flash('Error! Song with id "' . $id . '" not found');
        return \Redirect::route($this->view_idx);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreSongRequest $request, $id)
    {
        // get this Song
        Song::where('id', $id)
                ->update($request->except(['_method','_token']));

        flash('Song with id "' . $id . '" updated');
        return \Redirect::route($this->view_idx);
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
        $output = Song::find($id);
        if ($output) {
            $output->delete();
            flash('Song with id "' . $id . '" deleted.');
            return \Redirect::route($this->view_idx)
                            ->with(['status' => $message]);
        }
        //
        flash('Error! Song with ID "' . $id . '" not found');
        return \Redirect::route($this->view_idx);
    }
}
