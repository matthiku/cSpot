<?php

namespace App\Http\Controllers\Cspot;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;

use App\Models\Plan;
use App\Models\Team;
use App\Models\User;

use App\Mailers\AppMailer;

use Carbon\Carbon;
use Auth;



class TeamController extends Controller
{


    /**
     * Show form to manage team for a plan
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $plan_id)
    {
        // get current plan
        $plan = Plan::with('teams')->find($plan_id);
        // get list of users
        $users = User::where('name', '<>', 'n/a')->orderBy('first_name')->get();

        // produce array with users and all their roles
        $rou = User::with('roles')->get();
        $userRoles = [];
        foreach ($rou as $user) {
            $roles = [];
            foreach ($user->roles as $value) {
                if ($value->id > 3) { // no administrative roles needed
                    array_push($roles, ['role_id'=>$value->id, 'name'=>$value->name] ); }
            }
            $userRoles[$user->id] = ['name'=>$user->name, 'roles'=>$roles];       
        }


        // return view
        if ($plan) {
            return view('cspot.team', [
                'plan'      =>$plan, 
                'users'     =>$users, 
                'userRoles' =>json_encode($userRoles)
            ]);
        }
    }




    /**
     * Send an email to the user to request his confirmation for being a member of the team
     *
     * @return \Illuminate\Http\Response
     */
    public function sendrequest($plan_id, $id)
    {
        // get the resource handle
        $team = Team::find($id);
        if ($team) {
            if ($team->requested) {
                $error = 'Request Email was already sent to this user!';
                return \Redirect::back()
                                ->with(['error' => $error]);
            }
            $team->requested = True;
            $team->save();
            $status = 'Email with membership request was sent to user (ATM SIMULATED ONLYL!)';
            return \Redirect::route('team.index', ['plan_id'=>$plan_id])
                            ->with(['status' => $status]);
        }
        $error = 'Wrong team member id!';
        return \Redirect::back()
                        ->with(['error' => $error]);
    }



    /**
     * User confirms that he wants to be a member of the team
     *
     * @return \Illuminate\Http\Response
     */
    public function confirm($plan_id, $id)
    {
        // get the resource handle
        $team = Team::find($id);
        if ($team) {
            $team->confirmed = ! $team->confirmed;
            $team->save();
            if ($team->requested) {
                $status = 'You confirmed your membership for this plan.'; 
            }
            else {
                $status = 'You declined your membership for this plan.'; 
            }
            return \Redirect::route('team.index', ['plan_id'=>$plan_id])
                            ->with(['status' => $status]);
        }
        $error = 'Wrong team member id!';
        return \Redirect::back()
                        ->with(['error' => $error]);
    }





    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $plan_id)
    {
        // get current plan
        $plan = Plan::with('teams')->find($plan_id);

        // see if this was a submit with a new team member request
        if ($request->has('user_id') && $request->has('role_id') ) {
            // now we need to add this to the DB
            $team = new Team([
                'user_id' => $request->user_id, 
                'role_id' => $request->role_id,
                'comment' => $request->comment,
            ]);
            $plan->teams()->save($team);

            $status = 'New Team Member added.';
            return \Redirect::route('team.index', ['plan_id'=>$plan_id])
                            ->with(['status' => $status]);
        }
        $error = 'Wrong plan id!';
        return \Redirect::back()
                        ->with(['error' => $error]);
    }




    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        return 'UPDATE not implemented yet...';
    }





    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($plan_id, $id)
    {
        // get the resource handle
        $team = Team::find($id);
        if ($team) {
            $team->delete();
            $status = 'Team Member removed from this plan.';
            return \Redirect::route('team.index', ['plan_id'=>$plan_id])
                            ->with(['status' => $status]);
        }
        $error = 'Wrong team member id!';
        return \Redirect::back()
                        ->with(['error' => $error]);
    }
}
