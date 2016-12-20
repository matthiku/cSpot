<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

use App\Models\Item;
use App\Models\SongPart;

use Auth;


class HomeController extends Controller
{


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }




    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index()
    {
        return view('home');
    }




    /**
     * Show the welcome screen after a successful logon
     *
     * or another screen the user chose as his start page
     *
     * @return Response
     */
    public function welcome()
    {
        if (Auth::user()->startPage != '') {
            return redirect( Auth::user()->startPage );
        }
        elseif (Auth::user()->hasRole('musician')) {
            return redirect( route('next') );
        }
        return view('welcome');
    }

    /**
     * Provide various data to the frontend
     */
    public function APIconfigGet(Request $request)
    {
        $cSpot = [

            'user' => json_decode( json_encode(Auth::user()) ),

            'lastSongUpdated_at' => getLastSongUpdated_at(),

            'env' => [
                'songSelectUrl' => env("SONGSELECT_URL", 'https://songselect.ccli.com/Songs/'),
                'presentationEnableSync' => env('PRESENTATION_ENABLE_SYNC', 'false'),
            ],

            'const' => [],

            'routes' => [
                'apiNextEvent'  => route('api.next.event'        ),
                'apiAddFiles'   => route('cspot.api.addfile'     ),
                'apiAddNote'    => route('api.addNote'           ),
                'apiUpload'     => route('cspot.api.upload'      ),
                'apiItems'      => route('cspot.api.item'        ),
                'apiGetPlan'    => route('api.plan.get'          ),
                'apiItemUpdate' => route('cspot.api.item.update' ),
                'apiPlanUpdate' => route('api.plan.update'       ),
                'apiGetSongList'=> route('getsonglist'          ),
                'setPositionURL'=> route('presentation.position.set'),
                'eventSource'   => route('presentation.sync'),
                'apiItemsFileUnlink' => route('api.items.file.unlink' ),
                'apiSongsFileUnlink' => route('api.songs.file.unlink' ),
                'apiBibleBooksAllVerses' => route('bible.books.all.verses' ),
            ],

            'presentation' => [
                'sync' => false,
                'slide' => 'start',
                'mainPresenter' => getMainPresenter(),
                'mainPresenterSetURL' => route('presentation.mainPresenter.set'),
            ],

            'song_parts' => json_decode( json_encode(SongPart::orderby('sequence')->get()) ),
            'song_parts_by_code' => json_decode( json_encode(getSongPartsByCode()) ),
        ];

        // are we looking at a single item?
        $referer = explode( '/', $request->header('referer') );
        $len = count($referer);

        if ( $referer[$len-3] == 'items' )
            $item = Item::find( $referer[$len-2] );

        if ( $request->has('item_id')) 
            $item = Item::find( $request->item_id );

        if( isset($item) ) {

            $cSpot['presentation'] = [

                'sync' => false,
                'slide' => 'start',
                'mainPresenter' => getMainPresenter(),
                'mainPresenterSetURL' => route('presentation.mainPresenter.set'),

                'plan' => json_decode( json_encode($item->plan) ),

                // keep track of current background image
                'currentBGimage' => 0,
                'BGimageCount' => 0,

                // get relevant ids of current slides
                'plan_id' => $item->plan_id,
                'item_id' => $item->id,
                'seq_no'  => $item->seq_no,
                'max_seq_no' => $item->plan->lastItem()->seq_no,

                // set offline mode (using cached items) as default
                'useOfflineMode' => true,
            ];
        }

        return response()->json($cSpot); //addslashes( json_encode($cSpot, JSON_HEX_APOS | JSON_HEX_QUOT ) );
    }


}
