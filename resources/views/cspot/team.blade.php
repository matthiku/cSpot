
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', 'Team Management')

@section('plans', 'active')


@section('content')


    @include('layouts.flashing')

    @if ( $plan->teams->count() > 0 )
        <button onclick="$('#new-member-form').toggle();" class="pull-xs-right">Add new team member</button>
    @endif
    

    <h4>Team for "{{ $plan->type->name }}" on 
        <span class="hidden-md-down">{{ $plan->date->formatLocalized('%A, %d %B %Y') }}</span>
        <span class="hidden-lg-up"  >{{ $plan->date->formatLocalized('%a, %d %B')    }}</span>
    </h4>


    <!-- 
        show the current team members and their roles 
    -->
    <table class="table table-striped table-bordered 
                @if(count($plan->teams)>15)
                 table-sm
                @endif
                 ">
        <thead class="thead-default">
            <tr>
                <th>Name</th>
                <th>Role</th>
                <th class="hidden-sm-down">Comment</th>
                <th>Requested?</th>
                <th>Confirmed?</th>
                
                @if( Auth::user()->isEditor()  ||  Auth::user()->ownsPlan($team->plan_id) )
                <th>Action</th>
                @endif
            </tr>
        </thead>

        <tbody>
        @foreach( $plan->teams as $team )
            <tr class="link" onclick="location.href='{{ url('cspot/plans/'.$team->plan_id.'/team') }}/edit'">
                <td scope="row">{{ $team->user->name }}</td>

                <td>{{ ucfirst($team->role->name) }}</td>

                <td class="hidden-sm-down">{{ $team->comment }}</td>

                <td>
                    <a class="btn btn-secondary btn-sm pull-sm-right" title="Send request to user" 
                        href='{{ url('cspot/plans/'.$team->plan_id.'/team/'.$team->id) }}/sendrequest'><i class="fa fa-envelope-o"></i></a>
                    <i class="fa fa-{{ ($team->requested) ? 'check-square' : 'minus-square-o' }}"> </i> 
                </td>

                <td>
                    @if ( Auth::user()->id == $team->user_id )
                        <a class="btn btn-secondary btn-sm pull-sm-right" title="Confirm/Decline" 
                            href='{{ url('cspot/plans/'.$team->plan_id.'/team/'.$team->id) }}/confirm'>Confirm/Decline: <i class="fa fa-square-o"></i></a>
                    @endif
                    <i class="fa fa-{{ ($team->confirmed) ? 'check-square' : 'minus-square-o' }} fa-big"> </i>
                </td>

                @if( Auth::user()->isEditor()  ||  Auth::user()->ownsPlan($team->plan_id) )
                <td class="nowrap">
                        <a class="btn btn-primary-outline btn-sm" title="Edit" 
                            href='{{ url('cspot/plans/'.$team->plan_id.'/team/'.$team->id) }}/edit'><i class="fa fa-pencil"></i></a>
                        <a class="btn btn-danger btn-sm" title="Delete!" 
                            href='{{ url('cspot/plans/'.$team->plan_id.'/team/'.$team->id) }}/delete'><i class="fa fa-trash"></i></a>
                </td>
                @endif

            </tr>

        @endforeach
        </tbody>

    </table>



    <!-- 
        show form to enter a new team member 
    -->
    @if ( $plan->teams->count() == 0 )
        <h5>Add a new team member:</h5>
    @endif

        <form id="new-member-form" method="POST" 
        @if ( $plan->teams->count() > 0 )
            style="display: none"
        @endif
        >

            <input type="hidden" name="_method" value="POST">
            {{ csrf_field() }}

            <label class="form-control-label">1. Select a User:</label>
            <select name="user_id" class="form-control text-help c-select" onchange="showRoleSelect(this)">
                <option selected>
                    Select ...
                </option>
                @foreach ($users as $user)
                    @if ( ! $user->hasRole('retired') && $user->roles->count() > 0  )
                    <option 
                        @if(  ''<>old('user_id') && $user->id==old('user_id') )  
                                selected
                        @endif
                        value="{{ $user->id }}">{{ $user->name }}
                    </option>
                    @endif
                @endforeach
            </select>
            @if ($errors->has('user_id'))
                <br><span class="help-block">
                    <strong>{{ $errors->first('user_id') }}</strong>
                </span>
            @endif

            <div id="select-team-role" style="display: none">
                <label class="form-control-label">2. Select a role (one at a time):
                    <div class="c-inputs-stacked" id="select-role-box">

                    </div>
                </label>
            </div>

            <label class="form-control-label" id="comment-input" style="display: none">
                3. (optional) Add a comment:
                <input type="text" name="comment">
            </label>
            
            <div class="form-control-label">
                <button type="submit" id="submit-button" style="display: none">Submit</button>
            </div>

        </form>


    <script>
        var userRolesJSON  = '{!! $userRoles !!}';
        var userRolesArray = JSON.parse(userRolesJSON);
    </script>

@stop