<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Http\Controllers\Cspot;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\StorePlanRequest;
use App\Http\Controllers\Controller;

use App\Models\Plan;
use App\Models\Item;
use App\Models\Type;
use App\Models\User;
use App\Models\DefaultItem;

use App\Mailers\AppMailer;

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
        $this->middleware('role:author', ['only' => ['destroy'] ]  );
        $this->middleware('role:editor', ['only' => ['destroy', 'create'] ]  );
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
        $heading = 'Your Service Plans';
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

        $heading = 'Upcoming Service Plans';
        return view( $this->view_all, array('plans' => $plans, 'heading' => $heading) );
    }


    /**
     * Display the next Sunday's Service Plan
     *
     * WARNING: this depends on service type ids 0 and 1 to be Sunday Services!
     *
     * @return \Illuminate\Http\Response
     */
    public function nextSunday()
    {
        //
        $plan = Plan::with('type')
            ->whereDate('date', '>', Carbon::yesterday())
            ->whereBetween('type_id', [0,1])
            ->orderBy('date')
            ->first();

        // call the edit action for a single plan
        return $this->edit($plan->id);
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
            $heading = 'All Church Service Plans for ';
        } else {
            $plans = Plan::with('type')
                ->whereDate('date', '>', Carbon::yesterday())
                ->where('leader_id', $user_id)
                ->orWhere('teacher_id', $user_id)
                ->whereDate('date', '>', Carbon::yesterday())
                ->orderBy('date')
                ->get();
            $heading = 'Upcoming Church Service Plans for ';
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
        $plan = Plan::create( $request->all() );

        // set some defaults
        $plan->changer = Auth::user()->first_name;
        $plan->state = 1;
        $plan->save();

        // insert default items if requested
        if ($request->input('defaultItems')=='Y') {
            $dItems = DefaultItem::where('type_id', $plan->type_id)->get();
            $newItems = [];
            foreach ($dItems as $dItem) {
                array_push( $newItems, new Item(['seq_no'=>$dItem->seq_no, 'comment'=>$dItem->text]) );
            }
            $plan->items()->saveMany($newItems);
        }

        flash('New Plan added with id '.$plan->id);
        return \Redirect::route('cspot.plans.edit', $plan->id);
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
        $plan = Plan::with([
                'items' => function ($query) { $query->orderBy('seq_no'); }])
            ->find($id);

        if ($plan) {
            // get list of service types
            $types = Type::get();
            // get list of users
            $users = User::orderBy('first_name')->get();
            // get list of trashed items (if any)
            $trashedItems = Item::onlyTrashed()->where('plan_id', $id)->get();

            return view( 
                $this->view_one, 
                array(
                    'plan'         => $plan, 
                    'types'        => $types, 
                    'users'        => $users, 
                    'trashedItems' => $trashedItems, 
                ) 
            );
        }
        //
        flashError('Plan with id "' . $id . '" not found');
        return \Redirect::route($this->view_idx);
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

        flash('Plan with id "' . $id . '" updated');
        return redirect()->back();
    }


    public function addNote(Request $request, $id)
    {
        if (! $request->input('info')) {
            flashError('Note was empty, nothing saved...');
            return redirect()->back();
        }
        // update this Plan
        $plan = Plan::find($id);

        $changer = Auth::user()->first_name;
        $note = $plan->info . chr(0x0d) . 'Note from '. $changer.':'.chr(0x0d). $request->input('info');

        $plan->info = $note;
        $plan->save();

        flash('Note added.');
        return redirect()->back();
    }



    public function sendReminder(Request $request, $id, $user_id, AppMailer $mailer)
    {
        // find the Plan
        $plan = Plan::find($id);
        // get the recipient
        $recipient = User::find($user_id);
        // verify validity of this request
        if ($plan && $plan->isFuture() && Auth::user()->OwnsPlan($id) ) {

            $mailer->planReminder( $recipient, $plan );

            flash( 'Email sent to '.$recipient->getFullName() );
            return redirect()->back();
        }

        flash('Plan not found!');
        return redirect()->back();
    }





    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // find a single resource by ID
        $output = Plan::find($id);
        if ($output) {
            $items = $output->items()->get();
            if ( count($items) ) {
                flashError('Plan with ID "' . $id . '" still contains items and cannot be deleted. Please review this plan now.');
                return $this->edit($id);
            }
            $output->delete();
            flash('Plan with id "' . $id . '" deleted.');
            return \Redirect::route($this->view_idx);
        }
        //
        flashError('Plan with ID "' . $id . '" not found');
        return redirect()->back();
    }


}
