<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Http\Controllers\Cspot;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\StoreSongRequest;
use App\Http\Controllers\Controller;

use App\Models\Song;
use App\Models\Plan;
use App\Models\File;


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
        $songs = Song::orderBy('title')->get();

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
        // get list of license types first
        $l = new Song;
        $licensesEnum = $l->getLicenseEnum();

        return view($this->view_one, ['licensesEnum' => $licensesEnum]);
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
        flash('New Song added: '.$request->title );
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
            // get list of license types first
            $l = new Song;
            $licensesEnum = $l->getLicenseEnum();

            return view( $this->view_one, array(
                'song'         => $output, 
                'licensesEnum' => $licensesEnum,
                'plansUsingThisSong' => $output->plansUsingThisSong(),
            ));
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
        $song = Song::find($id);
        // handle error if song is not found!
        if (! $song) {
            flash('Song not found!!');
            return redirect()->back();
        }

        // handle file uplaods
        if ($request->hasFile('file')) {
            if ($request->file('file')->isValid()) {

                $extension = $request->file('file')->getClientOriginalExtension();
                $token     = str_random(32).'.'.$extension;
                $filename  = $request->file('file')->getClientOriginalName();

                // move the anonymous file to the central location
                $destinationPath = config('files.uploads.webpath');
                $request->file('file')->move($destinationPath, $token);

                $file = new File([
                    'token'    => $token,
                    'filename' => $filename
                ]);
                // add the file as a relationship to the song
                $song->files()->save($file);
            }
            else {
                flash('Uploaded file could not be validated!');
            }
        }

        // update from request
        $song->update($request->except(['_method','_token','youtube_id']));
        
        // handle yt id seperately in order to use the Song Model setter method
        $song->youtube_id = $request->youtube_id;
        $song->save();

        // instead of flashing, maybe show the field 'updated_at' in the form?
        flash( 'Song "'.$request->title.'" updated.' );
        return redirect()->back();
    }







    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //
        // find a single resource by ID
        $output = Song::find($id);
        if ($output) {
            $output->delete();
            flash( 'Song "'.$request->title.'" deleted.' );
            return \Redirect::route( $this->view_idx );
        }
        //
        flash('Error! Song with ID "' . $id . '" not found');
        return \Redirect::route($this->view_idx);
    }
}
