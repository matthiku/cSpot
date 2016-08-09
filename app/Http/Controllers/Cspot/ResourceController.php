<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Http\Controllers\Cspot;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Plan;
use App\Models\Resource;

use Auth;
use DB;


class ResourceController extends Controller
{



    /**
     * Show form to manage resource for a plan
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $plan_id)
    {
        // get current plan with resources
        $plan = Plan::with('resources')->find($plan_id);

        // get FULL list of resources
        $resources = Resource::get();

        // return view
        if ($plan) {
            return view('cspot.resources', [
                'plan'      =>$plan, 
                'resources' =>$resources, 
            ]);
        }
        flashError('Plan with ID "' . $id . '" not found');
        return redirect()->back();
    }



    /**
     * Attach a resource to the plan
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $plan_id)
    {
        // get current plan
        $plan = Plan::with('resources')->find($plan_id);

        // check access rights
        if (! Auth::user()->ownsPlan($plan_id) ) {
            return redirect('home')->with('error', 'You are unauthorized for this request.');
        }

        // see if this was a submit with a new team member request
        if ( $request->has('resource_id') ) {

            $res_id = $request->resource_id;

            // check if res is already attached to plan
            if ($plan->resources()->find($res_id)) {
                $status = 'Resource was already added to this plan!';
                return \Redirect::route('resource.index', ['plan_id'=>$plan_id])
                                ->with(['status' => $status]);
            }
            // now we can add this new resource to the Plan
            $plan->resources()->attach($res_id, [
                'comment' => $request->has('comment') ? $request->get('comment') : ''
            ]);

            $status = 'New Resource added.';
            return \Redirect::route('resource.index', ['plan_id'=>$plan_id])
                            ->with(['status' => $status]);
        }
        $error = 'Wrong plan id!';
        return \Redirect::back()
                        ->with(['error' => $error]);
    }



    /**
     * Remove a resource from a plan
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $plan_id, $res_id)
    {
        // check access rights
        if (! Auth::user()->ownsPlan($plan_id) ) {
            return redirect('home')->with('error', 'You are unauthorized for this request.');
        }

        // get current plan
        $plan = Plan::with('resources')->find($plan_id);

        // see if this was a submit with a new team member request
        if ( $plan ) {

            // check if res is actually attached to plan
            if ($plan->resources()->where('id', $res_id)) {
                // now we can remove the resource from the Plan
                $plan->resources()->detach($res_id, [
                    'comment' => $request->has('comment') ? $request->get('comment') : ''
                ]);
                $status = 'Resource removed from the plan.';
                return \Redirect::route('resource.index', ['plan_id'=>$plan_id])
                                ->with(['status' => $status]);
            }
            $status = 'Resource was now attached to this plan!';
            return \Redirect::route('resource.index', ['plan_id'=>$plan_id])
                            ->with(['status' => $status]);

        }
        $error = 'Wrong plan id!';
        return \Redirect::back()
                        ->with(['error' => $error]);
    }


    /**
     * Update a single field in the pivot table of the plan-attached resource
     */
    public function APIupdate(Request $request)
    {
        // check if all necessary elements are given
        if ($request->has('id') && $request->has('value') ) {
            $field_name = explode('-', $request->id)[0];
            $item_id    = explode('-', $request->id)[3];
        }
        else { 
            return false; }

        //we need to find the actual resource as attached to the plan!
        $item = DB::table('plan_resource')->where('id', $item_id)->first();

        if ($item) {

            // check authentication
            $plan = Plan::find( $item->plan_id );
            if (! checkRights($plan)) {
                return response()->json(['status' => 401, 'data' => 'Not authorized'], 401);
            }

            // update the given field with the given value
            DB::table('plan_resource')->where('id', $item_id)
                ->update( [$field_name => $request->value] );

            // return text to sender
            $result = DB::table('plan_resource')->where('id', $item_id)->first();
            return $result->{$field_name};
        }

        return response()->json(['status' => 404, 'data' => "APIupdate: item with id $item_id not found"], 404);

    }



}
