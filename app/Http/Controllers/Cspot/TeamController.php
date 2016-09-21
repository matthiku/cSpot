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
        // get current plan with team members ordered by roles
        $plan = Plan::with([
                'teams' => function ($query) { $query->orderBy('role_id'); }])
            ->find($plan_id);
        // get list of users
        $users = User::where('name', '<>', 'n/a')->orderBy('first_name')->get();

        // return view
        if ($plan) {
            return view('cspot.teams', [
                'plan'      =>$plan, 
                'users'     =>$users, 
                'userRoles' =>json_encode( getUsersRolesAndInstruments() ),
            ]);
        }
    }




    /**
     * Send an email to the user to request his confirmation for being a member of the team
     *
     * @return \Illuminate\Http\Response
     */
    public function sendrequest($plan_id, $id, AppMailer $mailer)
    {
        // check access rights
        if (! Auth::user()->ownsPlan($plan_id) ) {
            return redirect('home')->with('error', 'You are unauthorized for this request.');
        }

        // get the resource handle
        $team = Team::find($id);
        if ($team) {
            if ($team->requested) {
                $error = 'Request Email was already sent to this user!';
                return \Redirect::back()
                                ->with(['error' => $error]);
            }
            $team->requested = True;
            $team->remember_token = str_random(32);

            // send internal message to user
            $message = 'Please open <a href="' . url('cspot/plans/'.$plan_id) . '/team"> this plan </a> and confirm if you accept the given role.';
            $thread_id = sendInternalMessage('You have been assigned a role in a Service plan', $message, $team->user_id, false);

            $team->thread_id = $thread_id;
            $team->save();

            // also send an email to the user
            $recipient = User::find($team->user_id);
            $plan = Plan::find($team->plan_id);
            $mailer->getPlanMemberConfirmation( $recipient, $plan, $team );


            $status = 'Email with membership request was sent to user.';
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
    public function confirm($plan_id, $id, $token=null)
    {
        // get the resource handle
        $team = Team::find($id);

        // check access rights - only the actual user can confirm or reject the assignment
        if ( ! $token == $team->remember_token   &&  ! $team->user_id == Auth::user()->id ) {
            return redirect('home')->with('error', 'You are unauthorized for this request.');
        }
        // write the confirmation or rejection into the DB
        if ($team) {
            // if this request came via the direct link, it's always a confirmation
            if (!$token==null) {$team->confirmed=False;}
            // now reverse the setting
            $team->confirmed = ! $team->confirmed;
            if ($team->confirmed) {
                $team->available = True;
                $status = 'Thank you! Your partizipation is confirmed for this plan.'; 
            }
            else {
                // we also have to reset the availability
                $team->available = False;
                $status = 'Thank you! Your status was changed accordingly.'; 
            }
            // delete the confirmation request message thread, if there was any
            deleteConfirmRequestThread($team->thread_id);
            $team->thread_id = 0;
            $team->save();
            return \Redirect::route('team.index', ['plan_id'=>$plan_id])
                            ->with(['status' => $status]);
        }
        $error = 'Wrong team member id!';
        return \Redirect::back()
                        ->with(['error' => $error]);
    }




    /**
     * A user announced his availability for a certain plan
     * 
     * @param plan_id   integer 
     * @param available boolean 
     *
     * (This is an API type call)
     */
    public function available($plan_id, $available)
    {
        $bool = $available=='true' ? True : False;

        // get current plan
        $plan = Plan::with('teams')->find($plan_id);

        if ($plan->count()) {        
            $user_id = Auth::user()->id;

            // check if this user is already part of this plan
            $team = Team::where('plan_id', $plan_id)->where('user_id', $user_id)->first();
            if ($team) {
                // if the user has already be assigned a role, we only change their availability
                if ( $team->role_id || $team->confirmed || $bool) {
                    $team->update(['available' => $bool]);
                } else {
                    // if the user wasn't assigned a role yet, we can delete the record altogether.
                    $team->delete();
                }
            }
            else {
                // create a new team member record for this plan
                $team = new Team([
                    'user_id'   => $user_id, 
                    'available' => $bool,
                ]);
                $plan->teams()->save($team);
            }

            return response()->json("User's availability changed to ".$available, 200);
        }

        return response()->json("requested failed, no changes made!", 404);        
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

        // check access rights
        if (! Auth::user()->ownsPlan($plan_id) ) {
            return redirect('home')->with('error', 'You are unauthorized for this request.');
        }

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
     * Edit a single team member of a plan
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($plan_id, $id)
    {
        // get current plan
        $plan = Plan::with('teams')->find($plan_id);
        // check access rights
        if (! Auth::user()->ownsPlan($plan_id) ) {
            return redirect('home')->with('error', 'You are unauthorized for this request.');
        }

        // get list of users
        $users = User::where('name', '<>', 'n/a')->orderBy('first_name')->get();
        // get the resource handle
        $team = Team::find($id);
        if ($team) {
            return view('cspot.team', [
                'team'      =>$team, 
                'plan'      =>$plan, 
                'users'     =>$users, 
                'userRoles' =>json_encode( getUsersRolesAndInstruments() ),
            ]);
        }
        $error = 'Wrong team member id!';
        return \Redirect::back()
                        ->with(['error' => $error]);
    }





    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $plan_id 
     * @param  int  $id         team member id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $plan_id, $id)
    {
        // check access rights
        if (! Auth::user()->ownsPlan($plan_id) ) {
            return redirect('home')->with('error', 'You are unauthorized for this request.');
        }

        // get current team member
        $team = Team::find($id);

        // see if this was a submit with a new team member request
        if ( $team && $request->has('role_id') ) {
            // now we need to update the team member data
            $team->role_id = $request->role_id;
            $team->comment = $request->comment;
            $team->save();

            $status = 'Team Member data updated.';
            return \Redirect::route('team.index', ['plan_id'=>$plan_id])
                            ->with(['status' => $status]);
        }
        $error = 'Wrong team member id!';
        return \Redirect::back()
                        ->with(['error' => $error]);
    }





    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($plan_id, $id)
    {
        // check access rights
        if (! Auth::user()->ownsPlan($plan_id) ) {
            return redirect('home')->with('error', 'You are unauthorized for this request.');
        }

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
