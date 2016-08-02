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
    protected $currentShowPositionID = -1;
    protected $oldShowPositionID = -2;



    /**
     * Server-Sent event stream handling
     *
     */
    public function syncPresentation()
    {

        Log::info('setting: '.env('PRESENTATION_ENABLE_SYNC', 'false'));
        if ( ! env('PRESENTATION_ENABLE_SYNC', 'false') ) {
            return;
        }

        // define the new SSE stream
        $response = new StreamedResponse;
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set("X-Accel-Buffering", "no");
        $response->setCallback(function() {

            // get current data 
            $newMP  = $this->getNewestMainPresenter();
            $newPos = $this->getNewestShowPosition();

            // (nearly) endless loop to keep stream open
            // the client will automatically reconnect when we close the stream after a while.
            // this is ti avoid memory leaks and hangs on the server side!
            $count = 0;
            while (true) {

                // if we have a new show position, send it out
                if ( $newPos ) {
                    echo "event: syncPresentation\n";
                    echo "id: ".$count++."\n";
                    echo "data: ". json_encode($newPos) . "\n\n";
                    ob_flush(); flush();
                }

                // if we have a new MP, send it out
                if ( $newMP ) {
                    echo "event: newMainPresenter\n";
                    echo "id: ".$count++."\n";
                    echo "data: ". json_encode($newMP) . "\n\n";
                    ob_flush(); flush();
                }
                sleep(2);

                // get latest data (will be empty if there was no change)
                $newMP  = $this->getNewestMainPresenter();
                $newPos = $this->getNewestShowPosition();

                // close the stream after some time
                if (++$count > 100)
                    break;
            }
        });
        return $response;
    }


    protected function getNewestMainPresenter()
    {

        $mainPresenter = getMainPresenter();
        $this->currentMainPresenterID = $mainPresenter['id'];

        if ( $this->currentMainPresenterID != $this->oldMainPresenterID) {
            Log::info($this->oldMainPresenterID .' New MP found: '.$this->currentMainPresenterID);
            $this->oldMainPresenterID = $this->currentMainPresenterID;
            return $mainPresenter;
        }
        return '';
    }


    protected function getNewestShowPosition()
    {

        $showPosition = $this->getShowPosition();

        if ( ! ($this->currentShowPositionID == $this->oldShowPositionID) ) {
            Log::info( '---- New Show Position found: ' . json_encode($showPosition) );
            $this->oldShowPositionID = $this->currentShowPositionID;
            return $showPosition;
        }
        return '';
    }

    protected function getShowPosition()
    {
        // Do we already have a Main Presenter?
        if (Cache::has('showPosition')) {
            $showPosition = Cache::get('showPosition');
            //Log::info( 'Current Show Position is: ' . json_encode($showPosition) );
            $this->currentShowPositionID = $showPosition['id'];
        } 
        // there is no MP at the moment
        else {
            Log::info('No show position found!');
            $showPosition['id'] = 0;
            $showPosition['plan_id'] = 0;
            $showPosition['item_id'] = 0;
            $showPosition['slide'] = 'none';
            $this->currentShowPositionID = 0;
        }
        return $showPosition;
    }





    public function setPosition(Request $request)
    {
        // check if user is currently the MP
        if (Auth::user()->id != getMainPresenter()['id']) {
            return response()->json(['status' => 401, 'data' => 'Not a Main Presenter!'], 401);
        }
        // check if data is complete
        if ($request->has('plan_id') && $request->has('item_id') && $request->has('slide') ) {

            $data = $request->only(['plan_id', 'item_id', 'slide']);
            // add a random identifier string to it
            $data['id'] = random_int(1,9999999);
            // save to cache
            Cache::put('showPosition', $data, 600);

            Log::info('new show pos recvd: '.json_encode($data));

            return response()->json( ['status' => 202, 'data' => $data['id'] ], 202 );
        }

        return response()->json(['status' => 406, 'data' => 'Incomplete request!'], 406);
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

        // set default values if there is no MP at the moment
        $mainPresenter['id'] = 0;
        $mainPresenter['name'] = 'none';

        // Do we already have a Main Presenter?
        if (Cache::has('MainPresenter')) {
            $mainPresenter = Cache::get('MainPresenter');
            if ( $removeMe && $user->id == $mainPresenter['id'] ) {
                // the current Main Presenter wants to cease presenting
                Cache::forget('MainPresenter');
                return response()->json( ['status' => 205, 'data' => ''], 202 ); // 202 = "accepted"
            }
            // Only Admins can replace an existing presenter!
            if (! $user->isAdmin()) {
                return response()->json( ['status' => 202, 'data' => $mainPresenter], 202 );
            }
        }

        // no further action needed if user just want to state that he is not a Main Presenter...
        if ($removeMe) {
            return response()->json( ['status' => 205, 'data' => $mainPresenter], 202 ); // 202 = "accepted"
        }

        // save current user as Main Presenter  WAS: $user->toArray(); //
        $value =  ['id' => $user->id, 'name' => $user->name ];

        // set expiration date for this setting
        $expiresAt = Carbon::now()->addDays( env('PRESENTATION_EXPIRATION_DAYS', 1) );
        Cache::put( 'MainPresenter', $value, $expiresAt );

        Log::info('User became Main Presenter: (id:'.$user->id.') '.$user->name);

        return response()->json( ['status' => 201, 'data' => $value], 201 ); // 201 == "created"

    }
}


