<?php

# (C) 2016-2017 Matthias Kuhs, Ireland



/**
 * Provide the event data for various event views and API requests
 */



namespace App\Http\Controllers\Cspot;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\StorePlanRequest;
use App\Http\Controllers\Controller;

use App\Models\Plan;
use App\Models\Note;
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




class PlanController extends Controller
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
        flash('No upcoming Sunday Service plan found! Do you want to create one?');
        return redirect()->route('types.index');
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
     * API: Get Plan Data
     */
    public function APIgetPlan(Request $request)
    {
        if (! $request->has(('plan_id')))
            return response()->json(['status' => 405, 'data' => 'APIgetPlan: : incorrect parameters!']);

        $plan = Plan::find($request->plan_id);

        return response()->json($plan);
    }



    /**
     * API: Get List of Plans
     *
     * date - get plans for a certain date
     */
    public function APIgetPlanList(Request $request)
    {
        if (! $request->has(('date')))
            return response()->json(['status' => 405, 'data' => 'APIgetPlanList: : missing date parameter!']);

        $plans = Plan::where('date', 'like', $request->date.'%')->with('type')->get();

        return response()->json($plans);
    }





    /**
     * Display a List of Service Plans
     *    filtered by user (leader/teacher) or by plan type and/or ordered by certain fields
     *
     *
     * @param  filterby     (user|type|date|future)
     *                      Show only plans for a certain user, of a certain type, a certain date or all events
     *                      default is 'user'
     *
     * @param  filtervalue  (user_id|type_id|all)
     *                      default is plans for current user only
     *
     * @param  timeframe    (all|future)
     *                      Show only future plans or all
     *                      default is only future plans
     *
     * @param  orderBy      Field by which the list must be sorted by
     * @param  order        (desc|asc) default is 'asc' = ascending order
     *
     *
     * @return \Illuminate\Http\Response    Plans View
     *
     */
    public function index(Request $request)
    {
        // by default we use the current year, but check if request contains a year value
        $year = date("Y");
        if ($request->has('year'))
            $year = $request->year;

        if (isset($request->api))
            return getPlans($request);

        else {
            // for the calendar view, get all plans, yet filtered as requested
            $allPlans = getPlans($request)[0];
            $allPlans
                ->whereDate('date', '>=', Carbon::parse('first day of january '.$year))
                ->whereDate('date', '<', Carbon::parse('first day of january '.($year+1)));
        }

        // the request determines the heading for the page
        $getPlans = getPlans($request);
        $heading   = $getPlans[1];

        // for pagination, always append the original query string
        $querystringArray = $request->input();

        // limit even the listing of plans if the request contains a year
        if ($request->has('year')) {
            $year = $request->year;
            $somePlans = $getPlans[0]
                ->whereDate('date', '>=', Carbon::parse('first day of january '.$year))
                ->whereDate('date', '<', Carbon::parse('first day of january '.($year+1)))
                ->paginate(20)
                ->appends($querystringArray);
        }
        else $somePlans = $getPlans[0]
            ->paginate(20)
            ->appends($querystringArray);

        // get year of earliest plan
        $firstYear = Plan::orderBy('date')->first()->date->year;

        // provide all the data to and show the view
        return view(
            $this->view_all,
            array(
                'plans'     => $somePlans,
                'allPlans'  => $allPlans->get(),
                'heading'   => $heading,
                'userIsPlanMember' => listOfPlansForUser(),
                'types'     => Type::get(),
                'firstYear' => $firstYear
            )
        );
    }



    /**
     * Show events in a Year/Month Calendar View
     */
    public function calendar(Request $request)
    {
        $year = date("Y");
        if ($request->has('year'))
            $year = $request->year;

        $plans = Plan::with('type')
            ->whereDate('date', '>=', Carbon::parse('first day of january '.$year))
            ->whereDate('date', '<=', Carbon::createFromDate($year, 12, 31)->setTime(23,59,59))
            ->orderBy('date')
            ->get();

        //get the earliest year in which we have a plan
        $firstYear = Plan::orderBy('date')->first()->date->year;

        return view(
            'cspot.calendar',
            [
                'allPlans'  => $plans,
                'heading'   => 'Events for '.$year,
                'firstYear' => $firstYear
            ]
        );
    }



    /**
     * Display the plan for a specific date
     *
     * Show list of plans when there is more than one event per day
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
            'leader_id' => null,
            'subtitle'  => ''
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
            $request->session()->flash(
                'defaultValues', [
                    'type_id'   => $type->id,
                    'date'      => $newDate,
                    'start'     => $type->start,
                    'end'       => $type->end,
                    'leader_id' => $type->leader_id,
                    'subtitle'  => $type->subtitle,
                ]
            );
        }

        // was a date given in the URL query string?
        if ($request->has('date')) {
            // push plan date to session
            $request->session()->flash('defaultValues', [
                'type_id'   => null,
                'date'      => $request->get('date'),
                'start'     => '00:00',
                'end'       => '00:00',
                'leader_id' => null,
                'subtitle'  => ''
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
        $type = Type::find($plan->type_id);
        if ( count($type) && ! isset($request->start) ) {
            // default end time is only the time of day. We need to combine this with the plan date
            $startTme = Carbon::parse(   $type->start);
            $endTime  = Carbon::parse(   $type->end  );
        }
        else {
            // request contains custom start and end times
            $startTme = Carbon::parse(   $request->start );
            $endTime  = Carbon::parse(   $request->end   );
        }
        $plan->date     = $planDate->copy()->addHour($startTme->hour)->addMinute($startTme->minute);
        $plan->date_end = $planDate->addHour(         $endTime->hour)->addMinute( $endTime->minute);


        // verify 'private' status from request
        if ($request->has('private') && $request->private=='on')
            $plan->private = true;
        else
            $plan->private = false;


        $plan->save();

        addDefaultRolesAndResourcesToPlan($plan);

        // insert default items if requested
        if ($request->input('defaultItems')=='Y') {

            // get list of all default items for this plan type
            $dItems = DefaultItem::where('type_id', $plan->type_id)->get();

            // add each default item as new item to the new plan
            foreach ($dItems as $dItem) {
                // get single default item to create a nwe Item object
                $iNew = new Item([
                    'seq_no'            => $dItem->seq_no,
                    'comment'           => $dItem->text,
                    'forLeadersEyesOnly'=> $dItem->forLeadersEyesOnly,
                    'show_comment'      => $dItem->showItemText,
                    'key'               => $dItem->showAnnouncements ? 'announcements' : '',
                ]);
                // save the new item to the new plan
                $plan->items()->save($iNew);
                // if default item contains a default image, link the new Plan item to the image
                if ($dItem->file_id) {
                    $file = File::find($dItem->file_id);
                    if ($file)
                        $iNew->files()->save( $file );
                }
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
                'leader_id' => $plan->leader_id,
                'subtitle'  => $type->subtitle,
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
                'items' => function ($query) use ($id) {
                    if (! Auth::User()->ownsPlan($id) )
                        // do not provide the 'FLEO' items to non-leaders
                        $query->where('forLeadersEyesOnly', '<>', '1')->orderBy('seq_no');
                    else
                        $query->withTrashed()->orderBy('seq_no');
                }])
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
                'items' => function ($query) use ($id) {
                    if (! Auth::User()->ownsPlan($id) )
                        // do not provide the 'FLEO' items to non-leaders
                        $query->where('forLeadersEyesOnly', '<>', '1')->orderBy('seq_no');
                    else
                        $query->withTrashed()->orderBy('seq_no');
                }])
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

        // check if leader or teacher was changed and if reason was given
        if (! checkIfLeaderOrTeacherWasChanged( $request, $plan ))
            return redirect()->back(); // no reason was given for the change

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

        // verify 'private' status from request
        if ($request->has('private') && $request->private=='on')
            $plan->private = true;
        else
            $plan->private = false;
        $plan->save();


        flash('Plan with id "' . $id . '" updated');
        return redirect()->back();
    }



    public function sendReminder(Request $request, $id, $user_id, AppMailer $mailer)
    {
        // find the Plan
        $plan = Plan::find($id);
        // get the recipient
        $recipient = User::find($user_id);
        // what is the role of this user in this plan?
        $role = 'not set';
        if (isset($request->role))
            $role = $request->role;

        // verify validity of this request
        if ($plan && $plan->isFuture() && Auth::user()->ownsPlan($id) ) {

            $mailer->planReminder( $recipient, $plan, $role );

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

            // Plan notes are now seperate models belonging to a plan and a user
            $note = New Note;
            $note->text = $request->note;
            $note->user_id = Auth::user()->id;
            $plan->notes()->save($note);

            return $note->text;
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
                    if ($value==='_') $value='';
                    if ($value==='false') $value=0;
                    if ($value==='true' ) $value=1;
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
            // delete resources for this plan (if any)
            $plan->resources()->detach(); // as it is a Many-To-Many relationship....
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

            // keep only future plans in cache
            deleteCachedItemsFromPastPlans($plan_id);

            // is there already a value for that key?
            $cache = PlanCache::where('key', $request->key)->first();

            // NOTE: If the original length and the stored length of the value differs, itmight be because of an
            //       invalid charachter (higher Unicode) in the value, which cannot be stored in the database!

            if ($cache) {
                // yes, so just update the existing value
                $cache->update(['value' => $request->value]);
                // return ok response
                return response()->json(['status' => 200, 'data' => $cache->key." updated! Value length was ".strlen($cache->value)], 200);
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
            return response()->json(['status' => 200, 'data' => $cache->key." inserted! Value length was ".strlen($cache->value)], 200);
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
