<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Http\Controllers\Cspot;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\StorePlanRequest;
use App\Http\Controllers\Controller;

use App\Models\Plan;
use App\Models\PlanCache;
use App\Models\Team;
use App\Models\Item;
use App\Models\File;
use App\Models\Type;
use App\Models\User;
use App\Models\DefaultItem;

use App\Mailers\AppMailer;

use Carbon\Carbon;
use Auth;
use Log;


class 
PlanController extends Controller
{

    /**
     * define view names
     */
    protected $view_all = 'cspot.plans';
    protected $view_idx = 'plans.index';
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
     * @param bool $any return any type of plan or only sunday services
     * @param bool $api return plan data as JSON string
     *
     * @return \Illuminate\Http\Response
     */
    public function nextSunday($any=false, $api=false)
    {
        // perapre query builder statement
        $plan = Plan::with('type', 'resources');

        // all plans or just type 0 and 1?
        if (! $any) {
            $plan = $plan
                ->whereBetween('type_id', [0,1])
                ->whereDate('date', '>', Carbon::yesterday());
        } 
        // when all plans, then only thosw which haven't started yet
        else {
            $plan = $plan
                ->whereDate('date', '>', Carbon::now());
        }

        // from that list, git the oldest
        $plan = $plan
            ->orderBy('date')
            ->first();

        if ($api) {
            return $plan;
        }

        // issue #27 (error when no plan was found)
        if ($plan) {            
            // call the edit action for a single plan
            return $this->edit($plan->id);
        }
        flash('No upcoming Sunday Service plan found!');
        return redirect()->back();
    }


    /**
     * API: return next event of any type
     */
    public function APInextEvent()
    {
        $plan = $this->nextSunday(true, true);

        return response()->json($plan);
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
            $heading = 'Upcoming Services or Events';
            // get list of plans of which the current user is member
            $userIsPlanMember = listOfPlansForUser();
        }

        elseif ($filterby=='date') {
            // list only plans of a certain date
            $plans = Plan::with(['type', 'leader', 'teacher'])
                ->whereDate('date', 'like', '%'.$filtervalue.'%')
                ->orderBy($orderBy, $order);
            $heading = 'Events for '.Carbon::parse($filtervalue)->formatLocalized('%A, %d %B %Y');
        }

        // show only plans of the current user (or all plans if it's an admin)
        else
        {
            // show only the user's own plans 
            if ($show  =='all') {
                // past and future plans
                $plans = Plan::where('leader_id', Auth::user()->id)
                           ->orWhere('teacher_id', Auth::user()->id)
                              ->with('type')
                           ->orderBy($orderBy, $order);
                $heading = 'All Your Services/Events';
            } else {
                // only future plans
                $plans = Plan::with('type')
                        ->whereDate('date', '>', Carbon::yesterday())
                            ->where(function ($query) {
                                $query->where('leader_id', Auth::user()->id)
                                    ->orWhere('teacher_id', Auth::user()->id);
                                })
                          ->orderBy($orderBy, $order);
                $heading = 'Your Upcoming Services/Events';
            }
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
            ->where('date', 'like', $date.'%')->get();

        if ($plan->count()) {

            if ($plan->count()==1) {
                // call the edit action for a single plan
                return $this->edit($plan[0]->id);
            }

            $request->filterby = 'date';
            $request->filtervalue = $date;

            return $this->index($request);
        }

        // No plan found for this day, let the user create a new one

        // check if user is authorized to create a new plan....
        if (! Auth::user()->isAuthor() ) {
            flashError('No service plan for ' . Carbon::parse($date)->format('l jS \\of F Y') . ' found.');
            return \Redirect::back();
        }

        // push plan date to session
        $request->session()->flash('defaultValues', [
            'type_id'   => null,
            'date'      => $date,
            'start'     => '00:00',
            'end'       => '00:00',
            'leader_id' => null
        ]);
        // call plan creation method
        return $this->create($request);
    }






    /**
     * Show the form for creating a new plan.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Event Type is already defined in the request
        if ($request->has('type_id')) {

            // get the Event Type 
            $type = Type::find($request->type_id);

            // use heper function to calculate next date for this plan
            $newDate =  getTypeBasedPlanData($type);

            // send the default values to the View
            $request->session()->flash('defaultValues', [
                'type_id'   => $type->id,
                'date'      => $newDate,
                'start'     => $type->start,
                'end'       => $type->end,
                'leader_id' => $type->leader_id
            ]);
        }

        // get list of service types
        $types = Type::get();
        // get list of users
        $users = User::orderBy('first_name')->get();

        return view( $this->view_one, array('types' => $types, 'users' => $users) );
    }


    /**
     * Store a newly created plan in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePlanRequest $request)
    {
        // create new record
        $plan = Plan::create( $request->except(['start', 'end']) );

        // set some defaults
        $plan->changer = Auth::user()->first_name;
        $plan->state = 1;

        $planDate = Carbon::instance($plan->date);
        // insert default service TIMES if requested
        if ($request->input('defaultValues')=='Y') {
            $type = Type::find($plan->type_id);
            if (count($type)) {
                // default end time is only the time of day. We need to combine this with the plan date
                $startTme = Carbon::parse(   $type->start);
                $endTime  = Carbon::parse(   $type->end  );
                $plan->date     = $planDate->copy()->addHour($startTme->hour)->addMinute($startTme->minute);
                $plan->date_end = $planDate->addHour(         $endTime->hour)->addMinute( $endTime->minute);
            }
        }
        else {
            // request contains custom start and end times
            $startTme = Carbon::parse(   $request->start );
            $endTime  = Carbon::parse(   $request->end   );
            $plan->date     = $planDate->copy()->addHour($startTme->hour)->addMinute($startTme->minute);
            $plan->date_end = $planDate->addHour(         $endTime->hour)->addMinute( $endTime->minute);
        }

        $plan->save();

        addDefaultRolesAndResourcesToPlan($plan);

        // insert default items if requested
        if ($request->input('defaultItems')=='Y') {
            $dItems = DefaultItem::where('type_id', $plan->type_id)->get();
            // $newItems = [];
            foreach ($dItems as $dItem) {
                // get single default item to create a nwe Item object
                $iNew = new Item([
                    'seq_no'=>$dItem->seq_no, 
                    'comment'=>$dItem->text,
                    'forLeadersEyesOnly'=>$dItem->forLeadersEyesOnly
                ]);
                // save the new item to the new plan
                $plan->items()->save($iNew);
                // if default item contains a default image, link the new Plan item to the image
                if ($dItem->file_id) {
                    $file = File::find($dItem->file_id);
                    $iNew->files()->save( $file );
                }
                // array_push( $newItems, $iNew );
            }
        }

        flash('New Plan added with id '.$plan->id);
        // redirect back to the plan editor to create another plan
        if ($request->input('addAnother')=='Y') {

            // use heper function to calculate next date for this plan
            $newDate =  getNextPlanDate($plan);

            // send the default values to the View
            $request->session()->flash('defaultValues', [
                'type_id'   => $plan->type_id,
                'date'      => $newDate,
                'start'     => $startTme->toTimeString(),
                'end'       => $endTime->toTimeString(),
                'leader_id' => $plan->leader_id
            ]);

            // get list of service types
            $types = Type::get();
            // get list of users
            $users = User::orderBy('first_name')->get();
            return view( $this->view_one, array('types' => $types, 'users' => $users) );
        }

        return \Redirect::route('plans.edit', $plan->id);
    }







    /**
     * Display the specified plan.
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

        // find a single plan by ID
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

            // get service times from plan dates
            $plan->start = Carbon::instance($plan->date)->toTimeString();
            // for backwards compatibility, we allowed for null as end date
            if ($plan->date_end)
                $plan->end   = Carbon::instance($plan->date_end)->toTimeString();
            else
                $plan->end = "23:59";
            
            return view( 
                $this->view_one, 
                array(
                    'plan'         => $plan,
                    'types'        => $types, 
                    'users'        => $users, 
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
     * Update the specified plan in storage.
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

        // update Service Times
        //TODO: allow for end-time on the next day!
        $planDate = Carbon::parse($request->date );
        $startTme = Carbon::parse($request->start);
        $endTime  = Carbon::parse($request->end  );
        $plan->date     = $planDate->copy()->addHour($startTme->hour)->addMinute($startTme->minute);
        $plan->date_end = $planDate->addHour(         $endTime->hour)->addMinute( $endTime->minute);

        if ($endTime->lt($startTme))
            $plan->date_end = $plan->date_end->addDay();

        $plan->update( $request->except(['_method','_token','date','start','end']) );

        flash('Plan with id "' . $id . '" updated');
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

            flash( 'Email sent to '.$recipient->fullName );
            return redirect()->back();
        }

        flash('Plan not found!');
        return redirect()->back();
    }


    public function APIaddNote(Request $request)
    {
        if (! Auth::user()->isUser() )
            return response()->json(['status' => 401, 'data' => 'Not authorized'], 401);

        if ($request->has('id') && $request->has('note') ) {
            // update this Plan
            $plan = Plan::find($request->id);

            $changer = Auth::user()->first_name;
            $note = 'Note from '. $changer.': ('.Carbon::now()->formatLocalized('%e-%m-%g %H:%M').')'.chr(0x0a). $request->note;

            $plan->info = $plan->info . chr(0x0a) . chr(0x0a) . $note;
            $plan->save();

            return $note;
        }

        return response()->json(['status' => 405, 'data' => 'APIupdate: : incorrect parameters!']);
    }



    public function APIupdate(Request $request)
    {
        if (! Auth::user()->isEditor() )
            return response()->json(['status' => 401, 'data' => 'Not authorized'], 401);

        if ($request->has('id') && $request->has('value') ) {
            $arr_id = explode('-', $request->id);
            $field_name = $arr_id[0];
            $value = $request->value;

            if (count($arr_id)>2) {

                $plan_id    = $arr_id[3];

                // debug logging
                Log::debug('API Plan Update request - ID:'.$plan_id.', FIELD:'.$field_name.', VALUE:'.$value);

                // get plan object
                $plan = Plan::find($plan_id);
                if ($plan->count()) {
                    // allow for discarding the whole note
                    if ($value=='_') $value='';
                    // update plan data and return new field value
                    $plan->update([$field_name => $value]);
                    return $plan[$field_name];
                }
                return response()->json(['status' => 404, 'data' => "APIupdate: plan $plan_id not found!"], 404);
            }
        }
        return response()->json(['status' => 405, 'data' => 'APIupdate: : incorrect parameters!']);

    }



    /**
     * Remove the specified plan from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // find a single plan by ID
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




    /**
     * receive pre-rendered slides from client and buffer them for other users to download
     */
    public function postCache(Request $request, $plan_id)
    {
        // validation
        if (! $request->has('key') ) {
            return response()->json(['status' => 400, 'data' => "missing key or value!"], 400);
        }
        // allow for an empty value....
        if (! $request->has('value') ) {
            $request->value = ' ';
        }

        // make sure it's a valid plan id
        $plan = Plan::find($plan_id);

        // save key and value to local cache (replacing existing values)
        if ($plan->count()) {

            // is there alerady a value for that key?
            $cache = PlanCache::where('key', $request->key)->first();

            if ($cache) {
                // yes, so just update the existing value
                $cache->update(['value' => $request->value]);
                // return ok response
                return response()->json(['status' => 200, 'data' => "Updated!"], 200);
            } 

            // extract item id from request
            if ($request->has('item_id')) 
                $item_id = $request->item_id;
            else
                $item_id = 9999999;

            // no, so create a new key/value pair in the PlanCaches table
            $cache = new PlanCache([
                    'key'     => $request->key,
                    'value'   => $request->value,
                    'item_id' => $item_id
                ]);
            $plan->planCaches()->save($cache);
            
            // return ok response
            return response()->json(['status' => 200, 'data' => "Inserted!"], 200);
        }

        return response()->json(['status' => 404, 'data' => "plan with id $plan_id not found"], 404);
    }


    /**
     * receive pre-rendered slides from client and buffer them for other users to download
     */
    public function getCache($plan_id)
    {
        // make sure it's a valid plan id
        $plan = Plan::find($plan_id);

        if ($plan->count()) {

            // get cache for this plan
            $cache = $plan->planCaches()->get();

            // return ok response
            return response()->json(['status' => 200, 'data' => json_encode($cache)], 200);
        }

        return response()->json(['status' => 404, 'data' => "plan with id $plan_id not found"], 404);
    }


    /**
     * receive pre-rendered slides from client and buffer them for other users to download
     */
    public function deleteCache($plan_id)
    {
        // make sure it's a valid plan id
        $plan = Plan::find($plan_id);

        if ($plan->count()) {

            $cacheCount = $plan->planCaches()->count();

            // delete cached items from this plan
            $cache = $plan->planCaches()->delete();

            // return ok response
            return response()->json(['status' => 200, 'data' => $cacheCount.' cache-items deleted. '], 200);
        }

        return response()->json(['status' => 404, 'data' => "plan with id $plan_id not found"], 404);
    }

}
