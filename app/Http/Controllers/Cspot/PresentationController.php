<?php

namespace App\Http\Controllers\Cspot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Symfony\Component\HttpFoundation\StreamedResponse;

use Carbon\Carbon;
use Cache;
use Auth;

use Log;


class PresentationController extends Controller
{

    // watch changes of current MP
    protected $currentMainPresenterID = -1;
    protected $oldMainPresenterID = -2;


    public function syncPresentation()
    {
        $response = new StreamedResponse(function() {

            // get current MP
            $data = $this->getNewMainPresenter();

            // endless loop to keep stream open
            while (true) {
                // data will be empty if there was no change since last inquiry
                if ( $data ) {
                    Log::info('Sending new MP id: '. $data['id']);
                    echo "data: " .$data['id'] . "\n\n";
                    ob_flush();
                    flush();
                }
                sleep(2);
                // get latest data
                $data = $this->getNewMainPresenter();
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        return $response;

    }

    public function getNewMainPresenter()
    {
        // Do we already have a Main Presenter?
        if (Cache::has('MainPresenter')) {
            $mainPresenter = Cache::get('MainPresenter');
            $this->currentMainPresenterID = $mainPresenter['id'];
        } 
        // there is no MP at the moment
        else {
            $mainPresenter['id'] = 0;
            $this->currentMainPresenterID = 0;
        }
        // return the full value if the MP just changed
        if ( ! $this->currentMainPresenterID == $this->oldMainPresenterID) {
            Log::info($this->oldMainPresenterID .' New MP found: '.$this->currentMainPresenterID);
            $this->oldMainPresenterID = $this->currentMainPresenterID;
            return $mainPresenter;
        }
        return '';
    }






    /**
     * Set the current Main Presenter as the current user
     *
     * @return Response
     */
    public function setMainPresenter(Request $request)
    {
        $user = Auth::user()->find(Auth::user()->id);
        // not just anyone with a registration can set this....
        if ( ! $user->isUser() ) {
            return '';
        }

        // check if user wants to be removed
        $removeMe = ( $request->has('switch') && $request->switch == 'false' );

        if ($removeMe) {
            Log::info('User wants to stop being Main Presenter: (id: '.$user->id.') '.$user->name);
        }

        // Do we already have a Main Presenter?
        if (Cache::has('MainPresenter')) {
            $mainPresenter = Cache::get('MainPresenter');
            if ( $removeMe && $user->id == $mainPresenter['id'] ) {
                // the current Main Presenter wants to cease presenting
                Cache::forget('MainPresenter');
                return response()->json( ['status' => 205, 'data' => ''], 202 ); // 202 = "accepted"
            }
            return response()->json( ['status' => 202, 'data' => $mainPresenter], 202 );
        }
        // no further action needed if user just want to state that he is not a Main Presenter...
        if ($removeMe) {
            return response()->json( ['status' => 205, 'data' => ''], 202 ); // 202 = "accepted"
        }

        // save current user as Main Presenter
        $value = $user->toArray(); // (object) ['id' => $user->id, 'name' => $user->name ];
        // set expiration date for this setting
        $expiresAt = Carbon::now()->addDays( env('PRESENTATION_EXPIRATION_DAYS', 1) );
        Cache::put( 'MainPresenter', $value, $expiresAt );

        Log::info('User became Main Presenter: (id:'.$user->id.') '.$user->name);

        return response()->json( ['status' => 201, 'data' => $value], 201 ); // 201 == "created"

    }
}


