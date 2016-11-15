
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "User Profiles")




@section('content')


    @include('layouts.flashing')



    @if (isset($user))
        <h2>Update User</h2>
        {!! Form::model( $user, array('route' => array('users.update', $user->id), 'method' => 'put', 'id' => 'inputForm') ) !!}
    @else
        <h2>Create User</h2>
        {!! Form::open(array('action' => 'Admin\UserController@store', 'id' => 'inputForm')) !!}
    @endif


    <div class="row">

        <div class="col-md-6">

            <div class="row mb-1 full-width">
                <div class="col-sm-6 text-sm-right">
                    {!! Form::label('first_name', 'First Name') !!} <i class="red">*</i>
                </div>
                <div class="col-sm-6">
                   {!! Form::text('first_name'); !!}
               </div>
            </div>

            <div class="row mb-1 full-width">
                <div class="col-sm-6 text-sm-right">
                    {!! Form::label('last_name', 'Last Name') !!} <i class="red">*</i>
                </div>
                <div class="col-sm-6">
                    {!! Form::text('last_name'); !!}
               </div>
            </div>

            <div class="row mb-1 full-width">
                <div class="col-sm-6 text-sm-right">
                    {!! Form::label('name', 'Display Name (must be unique)') !!} <i class="red">*</i>
                </div>
                <div class="col-sm-6">
                    {!! Form::text('name'); !!}
               </div>
            </div>

            <div class="row mb-1 full-width">
                <div class="col-sm-6 text-sm-right">
                    {!! Form::label('email', 'Email Address') !!}
                </div>
                <div class="col-sm-6">
                    @if (Auth::user()->isAdmin())
                        {!! Form::text('email') !!}
                    @else
                        {!! Form::hidden('email') !!}
                        {{ isset($user) ? $user->email : '' }}
                    @endif
               </div>
            </div>

            
            <div class="row">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6">
                    @if (isset($user))
                        <p class="btn btn-secondary" onclick="$(this).children('input')[1].click();">
                            {!! Form::hidden('notify_by_email', '0') !!}
                            {!! Form::checkbox('notify_by_email', '1') !!}&nbsp;<small>Send me email notifications<br>of new internal messages</small>
                        </p>
                    @endif
                </div>
            </div>

            <div class="row mb-1 full-width">
                <div class="col-sm-6 text-sm-right">
                    <strong>Your Start Page</strong><br>
                    <small>(The page you are directed to after login)</small>
                </div>
                <div class="col-sm-6">
                    {!! Form::text('startPage'); !!}
               </div>
            </div>

            @if ($user->plans_as_leader->count()>0)
                <div class="row mb-1 full-width">
                    <div class="col-sm-6"></div>
                    <div class="col-sm-6">
                        <strong>
                            Was leader of
                            <span class="text-success">{{ $user->plans_as_leader->count() }}</span>
                            events
                        </strong>
                        <a href="{{ url('cspot/plans?filterby=user&filtervalue='.$user->id) }}&show=all"
                            title="show all plans of this user"><small> - show</small></a>
                   </div>
                </div>
            @endif

            @if ($user->plans_as_teacher->count()>0)
                <div class="row mb-1 full-width">
                    <div class="col-sm-6"></div>
                    <div class="col-sm-6">
                        <strong>
                            Was teacher in
                            <span class="text-success">{{ $user->plans_as_teacher->count() }}</span>
                            services
                        </strong>
                        <a href="{{ url('cspot/plans?filterby=user&filtervalue='.$user->id) }}&show=all"
                            title="show all plans of this user"><small> - show</small></a>
                   </div>
                </div>
            @endif


        </div>




        <div class="col-md-6">

            @if (Auth::user()->isAdmin())
                <div class="row">
                    <div class="col-sm-6">
                        <strong class="text-primary">Select Roles:</strong><br />
                        @foreach ($roles as $role)
                            <input name="{{ str_replace(' ','_',$role->name) }}" type="checkbox"
                                 {{ isset($user) && $user->hasRole($role->name) ? 'checked="checked"' : '' }}>
                            <label  for="{{ str_replace(' ','_',$role->name) }}">{{ $role->name }}</label><br />
                        @endforeach
                    </div>
                    <div class="col-sm-6">
                        <strong class="text-primary">Select Instruments:</strong><br />
                        @foreach ($instruments as $instrument)
                            <input name="{{ str_replace(' ','_',$instrument->name) }}" type="checkbox"
                                 {{ isset($user) && $user->hasInstrument($instrument->name) ? 'checked="checked"' : '' }}>
                            <label  for="{{ str_replace(' ','_',$instrument->name) }}">{{ $instrument->name }}</label><br />
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </div>

    <hr>


    <div class="row">
        <div class="col-md-6">
            <span><i class="red">*</i> = mandatory field(s) &nbsp;</span>

            @if (isset($user))

                <p class="float-xs-right">
                    {!! Form::submit('&#10003; &nbsp;  Update', ['class'=>'btn btn-primary']); !!}
                    @if (Auth::user()->isAdmin())
                        {{-- disable delete button if user owns plans --}}
                        <a class="btn btn-danger{{ $user->plans_as_leader->count() || $user->plans_as_teacher->count() ? ' disabled' : '' }}"
                            {{ $user->plans_as_leader->count() || $user->plans_as_teacher->count() ? 'aria-disabled="true"' : '' }}" 
                            href="{{ url('admin/users/'. $user->id) }}/delete">
                            <i class="fa fa-trash" > </i> &nbsp; Delete
                        </a>
                    @endif
                </p>
            @else
                <p class="float-xs-right">{!! Form::submit('&#10003; &nbsp;  Submit', ['class'=>'btn btn-primary']); !!}</p>
            @endif

            {!! Form::close() !!}
        </div>
    </div>

    <script type="text/javascript">document.forms.inputForm.first_name.focus()</script>
    

    
    @if (! Auth::user()->isAdmin())
        <hr>
        <div class="row mt-1">
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-5">
                @if ($user->roles->count())
                    <label>Your Roles:</label><br>
                    @foreach ($user->roles as $key => $role)
                        {{ ucfirst($role->name) }}{{ $key+1<$user->roles->count() ? ',' : '' }}
                    @endforeach
                @endif
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-5">
                @if ($user->instruments->count())
                    <label>Your Instruments:</label><br>
                    @foreach ($user->instruments as $instrument)
                        {{ ucfirst($instrument->name) }}{{ $key+1<$user->instruments->count() ? ',' : '' }}
                    @endforeach
                @endif
            </div>
        </div>
    @endif

@stop

