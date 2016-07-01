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
    public function index(Request $request )
    {
        $querystringArray = $request->input();
        // set default values
        $orderBy = isset($request->orderby) ? $request->orderby : 'title';
        $order   = isset($request->order)   ? $request->order   : 'asc';

        // with filtering?
        if (isset($request->filterby) and isset($request->filtervalue)) {
            if ($request->filterby=='fulltext') {
                $songs = Song::orderBy($orderBy, $order)
                    ->where(  'title',    'like', '%'.$request->filtervalue.'%')
                    ->orWhere('title_2',  'like', '%'.$request->filtervalue.'%')
                    ->orWhere('author',   'like', '%'.$request->filtervalue.'%')
                    ->orWhere('book_ref', 'like', '%'.$request->filtervalue.'%')
                    ->orWhere('lyrics',   'like', '%'.$request->filtervalue.'%');
            } 
            elseif ($request->filterby=='title') {
                $songs = Song::orderBy($orderBy, $order)
                    ->where(  'title',    'like', '%'.$request->filtervalue.'%')
                    ->orWhere('title_2',  'like', '%'.$request->filtervalue.'%');
            }
            else {
                $songs = Song::orderBy($orderBy, $order)
                    ->where($request->filterby, 'like', '%'.$request->filtervalue.'%');
            }
        } 
        else {
            $songs = Song::orderBy($orderBy, $order);
        }

        $heading = 'Manage Songs';
        // URL contains ...?plan_id=xxx (needed in order to add a song to that plan)
        $plan_id = 0;
        if ($request->has('plan_id')) {
            $plan_id = $request->plan_id;
            $heading = 'Select A Song For Your Plan';
        }

        // for pagination, always append the original query string
        $songs = $songs->paginate(20)->appends($querystringArray);

        return view( $this->view_all, array(
            'songs'       => $songs, 
            'heading'     => $heading,
            'plan_id'     => $plan_id,
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
        $song = Song::find($id);
        if ($song) {
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
                'song'         => $song, 
                'licensesEnum'   => $licensesEnum,
                'currentPage'      => $currentPage,
                'plansUsingThisSong' => $song->plansUsingThisSong(),
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
                // user helper function, save attached file and assign a file category of id 1 (song)
                $file = saveUploadedFile($request);
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
     * Search in the Song Database
     *
     * - - RESTful API request - -
     *
     *
     */
    public function searchSong(Request $request)
    {
        $result = false;
        if (isset($request->song_id) && $request->song_id>0) {
            $found = Song::find($request->song_id);
            $found->plans = $found->plansUsingThisSong();
            $result[0] = $found;
        }
        elseif (isset($request->search)) {
            // search
            $result = songSearch('%'.$request->search.'%');
            // get usage statistics
            foreach ($result as $song) {
                # get list of plans
                $song->plans = $song->plansUsingThisSong();
            }
        }
        if ($result) {
            // return to sender
            return response()->json(['status' => 200, 'data' => json_encode($result)]);
        }
        return response()->json(['status' => 404, 'data' => 'Not found'], 404);
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
