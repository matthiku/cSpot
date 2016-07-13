<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Http\Controllers\Cspot;

use Illuminate\Http\Request;

use Snap\BibleBooks\BibleBooks;

use App\Http\Requests;
use App\Http\Requests\StorePlanRequest;
use App\Http\Controllers\Controller;

use App\Models\Plan;
use App\Models\Team;
use App\Models\Item;
use App\Models\Type;
use App\Models\User;
use App\Models\DefaultItem;

use App\Mailers\AppMailer;

use Carbon\Carbon;
use Auth;
use Log;


class PlanController extends Controller
{

    /**
     * define view names
     */
    protected $view_all = 'cspot.plans';
    protected $view_idx = 'cspot.plans.index';
    protected $view_one = 'cspot.plan';



    /**
     * Authentication
     */
    public function __construct() {
        $this->middleware('role:author', ['only' => ['destroy'] ]  );
        $this->middleware('role:editor', ['only' => ['destroy', 'create'] ]  );
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

        // issue #27 (error when no plan was found)
        if ($plan) {            
            // call the edit action for a single plan
            return $this->edit($plan->id);
        }
        flash('No upcoming Sunday Service plan found!');
        return redirect()->back();
    }




    /**
     * Display a listing of Service Plans 
     *    filtered by user (leader/teacher) or by plan type
     *
     * @param  filter (user|type) Show only plans for a certain user or of a certain type
     * @param  value  user_id or type_id
     * @param  show   (all|future) Show only future plans or all 
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $querystringArray = $request->input();
        // set default values
        $filterby    = isset($request->filterby)    ? $request->filterby    : '';
        $filtervalue = isset($request->filtervalue )? $request->filtervalue : '';
        $show        = isset($request->show  )      ? $request->show        : 'future';
        $orderBy     = isset($request->orderby)     ? $request->orderby     : 'date';
        $order       = isset($request->order)       ? $request->order       : 'asc';

        $userIsPlanMember = [];
    
        // show only plans for certain user ids
        if ($filterby=='user') 
        {
            // show all plans, past and future?
            if ($show  =='all') {
                $plans = Plan::with('type')
                    ->where('leader_id', $filtervalue)
                    ->orWhere('teacher_id', $filtervalue)
                    ->orderBy($orderBy, $order);
                $heading = 'All Church Service Plans for ';
            } else {
                $plans = Plan::with('type')
                    ->whereDate('date', '>', Carbon::yesterday())
                    ->where('leader_id', $filtervalue)
                    ->orWhere('teacher_id', $filtervalue)
                    ->whereDate('date', '>', Carbon::yesterday())
                    ->orderBy($orderBy, $order);
                $heading = 'Upcoming Church Service Plans for ';
            }
            $heading .= User::find($filtervalue)->first_name;
        }
        // show only plans of certain type
        elseif ($filterby=='type') 
        {
            if ($show=='all') {
                $plans = Plan::with('type')
                    ->where('type_id', $filtervalue)
                    ->orderBy($orderBy, $order);
                $heading = 'Show All ';
            } else {
                $plans = Plan::with('type')
                    ->whereDate('date', '>', Carbon::yesterday())
                    ->where('type_id', $filtervalue)
                    ->orderBy($orderBy, $order);
                $heading = 'Show Upcoming ';
            }
            $heading .= Type::find($filtervalue)->name.'s';
        }
        // show all future plans
        elseif ($filterby=='future') {
            // get ALL future plans incl today
            $plans = Plan::with(['type', 'leader', 'teacher'])
                ->whereDate('date', '>', Carbon::yesterday())
                ->orderBy($orderBy, $order);

            // for an API call, return the raw data in json format (without pagination!)
            if (isset($request->api)) {
                return json_encode($plans->get());
            }
            $heading = 'Upcoming Service Plans';
            // get list of plans of which the current user is member
            $userIsPlanMember = listOfPlansForUser();
        }
        // show only plans of the current user (or all plans if it's an admin)
        else
        {
            // show all plans for Admins and only their own for non-Admins
            if (Auth::user()->isAdmin()) {
                $plans = Plan::with('type')
                          ->orderBy($orderBy, $order);
            } else {
                $plans = Plan::where('leader_id', Auth::user()->id)
                          ->orWhere('teacher_id', Auth::user()->id)
                          ->with('type')
                          ->orderBy($orderBy, $order);
            }
            $heading = 'Your Service Plans';
        }

        // for pagination, always append the original query string
        $plans = $plans->paginate(20)->appends($querystringArray);

        return view( 
            $this->view_all, 
            array('plans' => $plans, 'heading' => $heading, 'userIsPlanMember' => $userIsPlanMember) 
        );
    }





    /**
     * Display the plan for a specific date
     *
     * TODO: What happens if there is more than one event per day?
     *
     * @param  date  $date
     * @return \Illuminate\Http\Response
     */
    public function by_date(Request $request, $date)
    {
        // get plan with items ordered by seq no
        $plan = Plan::with([
                'items' => function ($query) { $query->orderBy('seq_no'); }])
            ->where('date', $date)->first();

        if ($plan) {
            $types = Type::get();
            // get list of users
            $users = User::orderBy('first_name')->get();

            return view( 
                $this->view_one, 
                array(
                    'plan'         => $plan, 
                    'types'        => $types, 
                    'users'        => $users, 
                    'mp_song_list' => MPsongList(),
                    'bibleBooks'   => new BibleBooks(),                     // array of bible books
                    'versionsEnum' => json_decode(env('BIBLE_VERSIONS')),   // array of possible bible versions
                    'newest_item_id'  => 0,
                    'trashedItemsCount' => 0, 
                )
            );
        }

        // No plan found for this day, let the user create a new one

        // check if user is authorized to create a new plan....
        if (! Auth::user()->isAuthor() ) {
            flashError('No service plan for ' . Carbon::parse($date)->format('l jS \\of F Y') . ' found.');
            return \Redirect::back();
        }

        // push plan date to session
        $request->session()->flash('defaultValues', ['date' => $date]);
        // call plan creation method
        return $this->create();
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

        addDefaultRolesToPlan($plan);

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
        // redirect back to the plan editor to create another plan
        if ($request->input('addAnother')=='Y') {
            // send default values for another plan in 7 days
            $newDate =  $plan->date->addDays(7);
            $request->session()->flash('defaultValues', ['type_id' => $plan->type_id, 'date' => $newDate]);

            // get list of service types
            $types = Type::get();
            // get list of users
            $users = User::orderBy('first_name')->get();
            return view( $this->view_one, array('types' => $types, 'users' => $users) );
        }

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
        // get plan with items ordered by seq no
        $plan = Plan::with([
                'items' => function ($query) { $query->orderBy('seq_no'); }])
            ->find($id);

        $types = Type::get();
        // get list of users
        $users = User::orderBy('first_name')->get();

        return view( 
            $this->view_one, 
            array(
                'plan'         => $plan, 
                'types'        => $types, 
                'users'        => $users, 
                'mp_song_list' => MPsongList(),
                'bibleBooks'   => new BibleBooks(),                     // array of bible books
                'versionsEnum' => json_decode(env('BIBLE_VERSIONS')),   // array of possible bible versions
                'newest_item_id'  => 0,
                'trashedItemsCount' => 0, 
            )
        );
    }





    

    /**
     * PLAN DETAILS form
     *
     * @param  int  $id
     * @param  int  $new_item_id    indicates a newly inserted item 
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        // find a single resource by ID
        $plan = Plan::with([
                'items' => function ($query) { $query->withTrashed()->orderBy('seq_no'); }])
            ->find($id);

        if ($plan) {
            // get list of service types
            $types = Type::get();
            // get list of users
            $users = User::orderBy('first_name')->get();
            // get list of trashed items (if any)
            $trashedItemsCount = Item::onlyTrashed()->where('plan_id', $id)->count();

            // check if a new item was just now inserted (used for highlighing in the view)
            $newest_item_id = 0;
            if (session()->has('newest_item_id')) {
                $newest_item_id = session()->get('newest_item_id');
                session()->forget('newest_item_id');
            }
            
            return view( 
                $this->view_one, 
                array(
                    'plan'         => $plan, 
                    'types'        => $types, 
                    'users'        => $users, 
                    'mp_song_list' => MPsongList(),
                    'bibleBooks'   => new BibleBooks(),                     // array of bible books
                    'versionsEnum' => json_decode(env('BIBLE_VERSIONS')),   // array of possible bible versions
                    'newest_item_id'  => $newest_item_id,
                    'trashedItemsCount' => $trashedItemsCount, 
                ) 
            );
        }
        
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

        // check if leader or teacher was changed
        checkIfLeaderOrTeacherWasChanged( $request, $plan );

        //$plan->save();
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
        $plan = Plan::find($id);
        if ($plan) {
            $items = $plan->items()->withTrashed()->get();
            if ( count($items) ) {
                flashError('Plan with ID "' . $id . '" still contains items (incl. binned items) and cannot be deleted. Please review this plan now.');
                return $this->edit($id);
            }
            // delete team members for this plan (if any)
            $plan->teams()->delete();
            // delete the plan
            $plan->delete();
            flash('Plan with id "' . $id . '" deleted.');
            return \Redirect::route($this->view_idx, ['filterby'=>'future']);
        }
        //
        flashError('Plan with ID "' . $id . '" not found');
        return redirect()->back();
    }


}
