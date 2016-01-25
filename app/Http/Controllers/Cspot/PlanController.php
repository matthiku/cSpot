<?php

namespace App\Http\Controllers\Cspot;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\StorePlanRequest;
use App\Http\Controllers\Controller;

use App\Models\Plan;
use App\Models\Item;
use App\Models\Type;
use App\Models\User;

use Carbon\Carbon;
use Auth;


class PlanController extends Controller
{

    /**
     * define view names
     */
    protected $view_all = 'cspot.plans';
    // protected $view_idx = 'cspot.plans.index';
    // instead of showing all plans, we will always redirect back to future plans 
    protected $view_idx = 'future';
    protected $view_one = 'cspot.plan';



    /**
     * Authentication
     */
    public function __construct() {
        $this->middleware('role:author', ['except' => ['index', 'show', 'future', 'edit', 'update', 'show']]);
        $this->middleware('role:editor', ['only' => ['destroy', 'create']]);
    }





    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // show all plans for Admins and only their own for non-Admins
        if (Auth::user()->isAdmin()) {
            $plans = Plan::with('type')->get();
        } else {
            $plans = Plan::where('leader_id', Auth::user()->id)
                      ->orWhere('teacher_id', Auth::user()->id)
                      ->with('type')->get();
        }
        $heading = 'Show Service Plans';
        return view( $this->view_all, array('plans' => $plans, 'heading' => $heading) );
    }



    /**
     * Display a listing of future Service Plans
     *
     * @return \Illuminate\Http\Response
     */
    public function future()
    {
        //
        $plans = Plan::with('type')
            ->whereDate('date', '>', Carbon::yesterday())
            ->orderBy('date')
            ->get();

        $heading = 'Show Upcoming Service Plans';
        return view( $this->view_all, array('plans' => $plans, 'heading' => $heading) );
    }



    /**
     * Display a listing of Service Plans filtered by user (leader/teacher)
     *
     * @return \Illuminate\Http\Response
     */
    public function by_user($user_id, $all=false)
    {
        //
        if ($all) {
            $plans = Plan::with('type')
                ->where('leader_id', $user_id)
                ->orWhere('teacher_id', $user_id)
                ->orderBy('date','DESC')
                ->get();
            $heading = 'Show All Church Service Plans for ';
        } else {
            $plans = Plan::with('type')
                ->whereDate('date', '>', Carbon::yesterday())
                ->where('leader_id', $user_id)
                ->orWhere('teacher_id', $user_id)
                ->whereDate('date', '>', Carbon::yesterday())
                ->orderBy('date')
                ->get();
            $heading = 'Show Upcoming Church Service Plans for ';
        }

        $heading .= User::find($user_id)->first_name;

        return view( 
            $this->view_all, 
            array('plans' => $plans, 'heading' => $heading) 
        );
    }


    /**
     * Display a listing of Service Plans filtered by user (leader/teacher)
     *
     * @return \Illuminate\Http\Response
     */
    public function by_type($type_id, $all=false)
    {
        //
        if ($all) {
            $plans = Plan::with('type')
                ->where('type_id', $type_id)
                ->orderBy('date','DESC')
                ->get();
            $heading = 'Show All ';
        } else {
            $plans = Plan::with('type')
                ->whereDate('date', '>', Carbon::yesterday())
                ->where('type_id', $type_id)
                ->orderBy('date')
                ->get();
            $heading = 'Show Upcoming ';
        }
        $heading .= Type::find($type_id)->name.'s';

        return view( 
            $this->view_all, 
            array('plans' => $plans, 'heading' => $heading) 
        );
    }





    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // get list of service types
        $types = Type::get();
        // get list of users
        $users = User::orderBy('first_name')->get();

        return view( $this->view_one, array('types' => $types, 'users' => $users) );
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePlanRequest $request)
    {
        // create new record
        $plan = Plan::create($request->all());
        $plan->changer = Auth::user()->first_name;
        $plan->state = 1;
        $plan->save();
        $status = 'New Plan added.';
        return \Redirect::route($this->view_idx)
                        ->with(['status' => $status]);
    }







    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // get all -- USERS -- with this specific plan id
        $plan    = Plan::find($id);
        $heading = 'Show '.$plan->name;
        return view( 'cspot.plan_full', array('plan' => $plan, 'heading' => $heading) );
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
        $output = Plan::with('items')->find($id);
        if ($output) {
            // get list of service types
            $types = Type::get();
            // get list of users
            $users = User::orderBy('first_name')->get();

            return view( $this->view_one, array('plan' => $output, 'types' => $types, 'users' => $users ) );
        }
        //
        $message = 'Error! Plan with id "' . $id . '" not found';
        return \Redirect::route($this->view_idx)
                        ->with(['status' => $message]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StorePlanRequest $request, $id)
    {
        // update this Plan
        $plan = Plan::find($id);
        $plan->changer = Auth::user()->first_name;
        $plan->save();
        $plan->update( $request->except(['_method','_token']) );

        $message = 'Plan with id "' . $id . '" updated';
        return \Redirect::route($this->view_idx)
                        ->with(['status' => $message]);
    }

    public function addNote(Request $request, $id)
    {
        // update this Plan
        $plan = Plan::find($id);

        $changer = Auth::user()->first_name;
        $note = $plan->info . '\nNote from '. $changer.'\n' . $request->input('info');

        $plan->info = $note;
        $plan->save();
        //$plan->update();

        $message = 'Plan with id "' . $id . '" updated';
        return \Redirect::route($this->view_idx)
                        ->with(['status' => $message]);
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
        $output = Plan::find($id);
        if ($output) {
            $output->delete();
            $message = 'Plan with id "' . $id . '" deleted.';
            return \Redirect::route($this->view_idx)
                            ->with(['status' => $message]);
        }
        //
        $message = 'Error! Plan with ID "' . $id . '" not found';
        return \Redirect::route($this->view_idx)
                        ->with(['status' => $message]);
    }


}
