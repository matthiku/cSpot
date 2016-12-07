<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Http\Controllers\Cspot;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\StoreSongRequest;
use App\Http\Controllers\Controller;

use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;

use App\Models\Song;
use App\Models\SongPart;
use App\Models\OnSong;
use App\Models\Plan;
use App\Models\File;

use Storage;
use Auth;
use DB;


class SongController extends Controller
{

    /**
     * define view names
     */
    protected $view_all = 'cspot.songs';
    protected $view_idx = 'songs.index';
    protected $view_one = 'cspot.song';



    /**
     * Authentication
     */
    public function __construct() {
        $this->middleware('role:editor', ['except' => ['index', 'show', 'APIgetSongList', 'searchSong', 'trainingVideos']]);
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
        $order   = isset($request->order)   ? $request->order   : 'ASC';

        // with filtering?
        if (isset($request->filterby) && $request->filterby!='songs' && isset($request->filtervalue)) {
            // we need to set the Pagination currentPage to 0,
            // otherwise we would not see the search results
            if (isset($request->page)) {
                $currentPage = 0;
                Paginator::currentPageResolver(function() use ($currentPage) {
                    return $currentPage;
                });
                $querystringArray = [];
            }

            if ($request->filterby=='fulltext') {
                $songs = Song::withCount('items')
                    ->orderBy($orderBy, $order)
                    ->where(  'title',    'like', '%'.$request->filtervalue.'%')
                    ->orWhere('title_2',  'like', '%'.$request->filtervalue.'%')
                    ->orWhere('author',   'like', '%'.$request->filtervalue.'%')
                    ->orWhere('book_ref', 'like', '%'.$request->filtervalue.'%')
                    ->orWhere('lyrics',   'like', '%'.$request->filtervalue.'%');
            } 
            elseif ($request->filterby=='title') {
                $songs = Song::withCount('items')
                    ->orderBy($orderBy, $order)
                    ->where(  'title',    'like', '%'.$request->filtervalue.'%')
                    ->orWhere('title_2',  'like', '%'.$request->filtervalue.'%');
            }
            elseif ($request->filtervalue=='_') {
                $songs = Song::withCount('items')
                    ->orderBy($orderBy, $order)
                    ->where(  $request->filterby, null)
                    ->orWhere($request->filterby, '');
            }
            else {
                $songs = Song::withCount('items')
                    ->orderBy($orderBy, $order)
                    ->where(  $request->filterby, 'like', '%'.$request->filtervalue.'%');
            }
        } 

        // no filter requested - show JUST the songs
        else {
            // (the where clause is needed since the 'withCount' would bring in all items with song_id = 0)
            $songs = Song::withCount('items')
                ->whereNotIn('title_2', ['video', 'slides', 'training'])
                ->orderBy($orderBy, $order);
        }

        // if orderBy is 'book_ref', then exclude all songs without a book_ref!
        if ($request->orderby == 'book_ref')
            $songs = $songs->where('book_ref', '<>', '');
        if ($request->orderby == 'author')
            $songs = $songs->where('author', '<>', '');

        // special case for OrderBy Last Time Used (in a plan!)
        if ($request->orderby == 'last_used') {
            $songs = Song::withCount('items')
                ->leftJoin('items', 'items.song_id', '=', 'songs.id')
                ->leftJoin('plans', 'plans.id', '=', 'items.plan_id')
                ->groupBy('songs.id')
                ->selectRaw('songs.*, max(plans.date) as last_used')
                ->orderBy('last_used', $order);
        }



        $heading = 'Songs Repository';

        if ( isset($request->filterby) && $request->filtervalue=='video' ) 
            $heading = 'Manage Videoclips';

        if ( isset($request->filterby) && $request->filtervalue=='slides' )
            $heading = 'Manage Slides';

        // Get list of rarely used songs
        if (isset($request->filterby) && $request->filterby=='songs' && isset($request->filtervalue) && $request->filtervalue=='rare') {
            $heading = 'Rarely Used Songs';
            $songs = Song::withCount('items') 
                ->leftJoin('items', 'items.song_id', '=', 'songs.id')
                ->leftJoin('plans', 'plans.id', '=', 'items.plan_id')
                ->groupBy('songs.id')
                ->selectRaw('songs.*, max(plans.date) as last_used')
                ->havingRaw('count(items.id) > 1')
                ->orderBy('last_used', 'ASC');
        }

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
     * show list of training videos
     */
    public function trainingVideos()
    {
        $songs = Song::where('title_2', 'training')
            ->orderBy('book_ref')
            ->get();

        $heading = 'c-Spot Training Videos';

        return view( 'cspot.training', array(
            'songs'       => $songs, 
            'heading'     => $heading,
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
            'songParts'    => SongPart::orderby('sequence')->get(),
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
        $song = Song::create($request->all());


        $type = 'song';
        // is this a videoclip or slideshow?
        if ( $song->title_2=='video' || $song->title_2=='slides'  || $song->title_2=='training' ) {
            $song->update(['license' => 'PD']);
            $type = $song->title_2;
        }

        // default route for 'normal' songs 
        if ($type=='song') {
            flash('New Song or Item added: '.$request->title );
            return \Redirect::route( $this->view_idx );
        }

        flash('New '.$song->title_2.' added, titled: '.$request->title );

        // show list of training videos if this was a new one of those
        if ($type=='training')
            return \Redirect::route( 'trainingVideos' );

        // go back to the list of slides or clips if we just created a new slide or clip
        return redirect()->route(
            $this->view_idx, [
                'filterby' => 'title_2',
                'filtervalue' => $song->title_2
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
        // get all -- PLANS -- with this specific song id
        $song    = Song::find($id);

        // find plans using this song
        $plans = $song->plansUsingThisSong();

        if ($plans) {
            $heading = 'Show Plans using the Song "'.$song->title.'"';
            return view( 'cspot.plans', [
                'plans' => $plans, 
                'heading' => $heading,
                'songParts' => SongPart::orderby('sequence')->get(),
            ]);
        }

        flash('No plans for this song found!');
        return \Redirect::back();        
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
                'songParts'         => SongPart::orderby('sequence')->get(),
                'plansUsingThisSong' => $song->allPlansUsingThisSong(),
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
                // set filename as Book Ref plus Song Title
                $file->filename = ($song->book_ref ? $song->book_ref : '') . ' - ' . $song->title;
                $file->save();
            }
            else {
                flash('Uploaded file could not be validated!');
            }
        }

        // set license type to CCLI if not set but CCLI_NO was given
        if (  $request->has('ccli_no') 
        && ( !$request->has('license') || ($request->has('license') && $request->license=='') )  ) {
            $song->license = 'CCLI';
        }

        // set license type to PD if not set but hymnaldotnet_id was given
        if (  $request->has('hymnaldotnet_id') 
        && ( !$request->has('license') || ($request->has('license') && $request->license=='') )  ) {
            $song->license = 'PD';
        }

        // update from request
        $song->update($request->except(['_method','_token','youtube_id']));
        
        // handle yt id seperately in order to use the Song Model setter method
        $song->youtube_id = $request->youtube_id;
        $song->save();

        // make sure no chached item refers to a changed song
        deleteCachedItemsContainingSongId( $song );

        // get the Pagination
        $currentPage = 9;
        if ($request->has('currentPage')) {
            $currentPage = $request->currentPage;
        } 

        // instead of flashing, maybe show the field 'updated_at' in the form?
        flash( 'Song or item "'.$request->title.'" updated.' );
        return redirect()->back()->with('currentPage', $currentPage);
    }



    /**
     * API - update single fields of item via AJAX
     */
    public function APIupdate(Request $request)
    {
        if ( ! $request->has('id') ) {
            // request was incomplete!
            return response()->json(['status' => 404, 'data' => 'API: item id missing!'], 404);
        }

        // if the value is missing, that means we just set it to blank (empty)
        if ( ! $request->has('value') ) {
            $request->value = '';
        }

        // the id field in the request was taken from the 'id' attribute 
        //      of the html element that triggered this request.
        //  It's format is: <field_name>-song-id-<song_id>
        $identity   = explode('-', $request->id);
        $field_name = $identity[0];
        $song_id    = $identity[3];


        // find the single resource
        $song = Song::find($song_id);

        if ( $song ) {

            // check authentication
            if ( ! Auth::user()->isEditor() )  {
                return response()->json(['status' => 401, 'data' => 'Not authorized'], 401);
            }
            // perform the update
            $song->update( [$field_name => $request->value] );

            // delete possible cached items which contain this song
            deleteCachedItemsContainingSongId( $song );
        
            // return text to sender
            return $song[$field_name];
        }
    }


    /**
     * Unlink a file attachment
     *
     * - - RESTful API request - -
     *
     */
    public function APIunlink(Request $request)
    {
        $song_id = $request->has('song_id') ? $request->song_id : 0;
        $file_id = $request->has('file_id') ? $request->file_id : 0;

        if (!$song_id || !$file_id)
            return response()->json(['status' => 404, 'data' => 'API: songId or fileId missing!'], 404);

        // find the single resource
        $file = File::find($file_id);
        if ($file && $file->song_id == $song_id) {

            $file->song_id = null;
            $file->save();

            // return to sender
            return response()->json(['status' => 200, 'data' => 'File unlinked!']);
        }

        return response()->json(['status' => 404, 'data' => 'File with id '.$file_id.' not found or song is not linked to this file!'], 404);
    }



    /**
     * Update or add an OnSong formatted song part
     *
     * - - RESTful API request - -
     *
     */
    public function APIupdateOnSongParts(Request $request)
    {
        // check authentication
        if ( ! Auth::user()->isEditor() )  {
            return response()->json(['status' => 401, 'data' => 'Not authorized'], 401);
        }

        // only for changing existing song parts
        $onsong_id = $request->has('onsong_id') ? $request->onsong_id : 0;
        // verify song id, part_id and onsong text
        $song_id = $request->has('song_id') ? $request->song_id : 0;
        $part_id = $request->has('part_id') ? $request->part_id : 0;
        $text    = $request->has('text')    ? $request->text    : 0;
        if ($song_id===0 || $part_id===0 || $text===0) 
            return response()->json(['status' => 400, 'data' => 'API: songId or part_id or text missing!'], 400);

        // is this a request to update an existing song part?
        if ($onsong_id) {
            $song_text = OnSong::with('song_part')->find($onsong_id);
            if ($song_text) {
                // is this a request to delete this item?
                if ($text=='_') {
                    $song_text->delete();
                    return response()->json(['status' => 202, 'data' => '_'], 202);
                }
                $song_text->text = $text;
                $song_text->save();
                return response()->json(['status' => 200, 'data' => $song_text], 200);
            }
        }

        // find the single corresponding song
        $song = Song::find($song_id);

        if ( $song ) {

            // create a new onSong record and link it with the song
            $song_text = new OnSong(['song_part_id' => $part_id, 'text' => $text]);

            // perform the update
            $song->onsongs()->save($song_text);

            // reload the new onsong part and link it with('song_part')

            // delete possible cached items which contain this song
            deleteCachedItemsContainingSongId( $song );

            // return the new record as JSON string
            return response()->json(['status' => 201, 'data' => $song_text], 201);
        }

        // provided or local data was incoherent
        return response()->json(['status' => 409, 'data' => 'API: conflicting data received!'], 409);
    }



    /**
     * API - get list of song titles for easy search
     */
    public function APIgetSongList()
    {
        return json_encode( MPsongList(), JSON_HEX_APOS | JSON_HEX_QUOT );
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
        // song was already selected
        if (isset($request->song_id) && intval($request->song_id)>0) {
            $found = Song::find($request->song_id);
            if ($found) {
                $found->plans = $found->plansUsingThisSong();
                $result[0] = $found;
            }
        }
        // we are still searching....
        elseif (isset($request->search)) {
            // search
            $result = songSearch('%'.$request->search.'%');
            // get usage statistics
            foreach ($result as $song) {
                # get list of plans
                $song->plans = $song->plansUsingThisSong();
            }
        }
        if (count($result)) {
            // return to sender
            return response()->json(['status' => 200, 'data' => json_encode($result)]);
        }
        return response()->json(['status' => 404, 'data' => 'Not found'], 404);
    }



    /**
     * Remove the specified resource from storage. Note: SoftDeletes !!
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
            flash( 'Song "'.$output->title.'" deleted.' );
            return \Redirect::back();
        }
        //
        flash('Error! Song with ID "' . $id . '" not found');
        return \Redirect::back();
    }
}
