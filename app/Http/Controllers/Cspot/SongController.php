<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Http\Controllers\Cspot;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\StoreSongRequest;
use App\Http\Controllers\Controller;

use Illuminate\Http\Response;

use App\Models\Song;
use App\Models\Plan;
use App\Models\File;

use Storage;
use Auth;


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
        $songs = Song::orderBy('title')->paginate(20);

        $heading = 'Manage Songs';
        return view( $this->view_all, array(
            'songs'       => $songs, 
            'heading'     => $heading,
            'currentPage' => $songs->currentPage(),
        ));
    }





    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // get list of license types first
        $l = new Song;
        $licensesEnum = $l->getLicenseEnum();

        return view($this->view_one, [
            'licensesEnum' => $licensesEnum,
            'currentPage'  => $request->currentPage,
        ]);
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
    public function edit(Request $request, $id)
    {
        // find a single resource by ID
        $output = Song::find($id);
        if ($output) {
            // get the Pagination
            if ($request->has('currentPage')) {
                $currentPage = $request->currentPage;
            } 
            elseif ( strpos($request->server('HTTP_REFERER'), '=' ) !== FALSE ) {
                $currentPage = explode('=', $request->server('HTTP_REFERER'))[1];
                if (! is_numeric($currentPage)) { $currentPage = 0; }
            }
            else {
                $currentPage = 0;
            }

            // get list of license types first
            $l = new Song;
            $licensesEnum = $l->getLicenseEnum();

            return view( $this->view_one, array(
                'song'         => $output, 
                'licensesEnum'   => $licensesEnum,
                'currentPage'      => $currentPage,
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
                // get file details etc
                $extension = $request->file('file')->getClientOriginalExtension();
                $token     = str_random(32).'.'.$extension;
                $filename  = $request->file('file')->getClientOriginalName();
                // move the anonymous file to the central location
                $destinationPath = config('files.uploads.webpath');
                $request->file('file')->move($destinationPath, $token);
                // user helper function
                $file = saveFile($request);
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

        // get the Pagination
        $currentPage = 9;
        if ($request->has('currentPage')) {
            $currentPage = $request->currentPage;
        } 

        // instead of flashing, maybe show the field 'updated_at' in the form?
        flash( 'Song "'.$request->title.'" updated.' );
        return redirect()->back()->with('currentPage', $currentPage);
    }


    /**
     * Remove a file attachment
     *
     * - - RESTful API request - -
     *
     * @param int $id
     *
     */
    public function deleteFile($id)
    {
        // find the single resource
        $file = File::find($id);
        if ($file) {
            // check authentication
            if (! Auth::user()->isAdmin() ) {
                return response()->json(['status' => 401, 'data' => 'Not authorized'], 401);
            }
            // delete the physical file
            $destinationPath = config('files.uploads.webpath');
            unlink(public_path().'/'.$destinationPath.'/'.$file->token);
            // delete the DB record
            $file->delete();
            // return to sender
            return response()->json(['status' => 200, 'data' => $file->token.' deleted.']);
        }
        return response()->json(['status' => 402, 'data' => 'Not found'], 402);
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
