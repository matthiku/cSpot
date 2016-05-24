
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', 'Team Management')

@section('plans', 'active')


@section('content')


    @include('layouts.flashing')
    

    <h4>Team member of Plan for 
        <a href="{{ url('cspot/plans/'.$plan->id) }}">
            "{{ $plan->type->name }}" on 
            <span class="hidden-md-down">{{ $plan->date->formatLocalized('%A, %d %B %Y') }}</span>
            <span class="hidden-lg-up"  >{{ $plan->date->formatLocalized('%a, %d %B')    }}</span>
        </a>
    </h4>



    <!-- 
        show form to update a team member 
    -->
    <h5>Edit information about a team member:</h5>

    <form id="new-member-form" method="POST" action="{{ url('cspot/plans/'.$team->plan_id.'/team/'.$team->id) }}/update">

        <input type="hidden" name="_method" value="POST">
        {{ csrf_field() }}

        <label class="form-control-label">Name:
            <input type="hidden" name="user-id" disabled="disabled" id="user-id" value="{{ $team->user_id }}">{{ $team->user->name }}
        </label>

        <div class="m-l-2" id="show-instruments"></div>

        <div id="select-team-role">
            <label class="form-control-label">Role:
                <div class="c-inputs-stacked m-l-1" id="select-role-box">
                    @foreach ($team->user->roles as $role)
                        <label class="c-input c-radio role-selector-items">
                            <input type="radio" id="role_id-{{ $role->id }}" value="{{ $role->id }}" name="role_id"
                                {{ $team->role_id == $role->id ? 'checked' : '' }}>
                            <span class="c-indicator"></span>{{ $role->name }}
                        </label>
                    @endforeach
                </div>
            </label>
        </div>

        <label class="form-control-label" id="comment-input">
            Comment (optional):
            <input type="text" name="comment" value="{{ $team->comment }}">
        </label>
        
        <div class="form-control-label">
            <button type="submit" id="submit-button">Submit changes</button>
        </div>

    </form>


    <script>
        var userRolesJSON  = '{!! $userRoles !!}';
        var userRolesArray = JSON.parse(userRolesJSON);
        showRoleSelect( $('#user-id')[0], {{ $team->role_id }} );
    </script>

@stop