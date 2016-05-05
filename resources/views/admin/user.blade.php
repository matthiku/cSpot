
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Create or Update a User")




@section('content')


    @include('layouts.flashing')


    @if (isset($user))
        <h2>Update User</h2>
        {!! Form::model( $user, array('route' => array('admin.users.update', $user->id), 'method' => 'put', 'id' => 'inputForm') ) !!}
    @else
        <h2>Create User</h2>
        {!! Form::open(array('action' => 'Admin\UserController@store', 'id' => 'inputForm')) !!}
    @endif

        <p>{!! Form::label('first_name', 'First Name') !!}<br>
           {!! Form::text('first_name'); !!}</p>

        <p>{!! Form::label('last_name', 'Last Name') !!}<br>
           {!! Form::text('last_name'); !!}</p>

        <p>{!! Form::label('name', 'Display Name (must be unique)') !!}<br>
           {!! Form::text('name'); !!}</p>

        <p>{!! Form::label('email', 'Email Address') !!}<br>
        @if (Auth::user()->isAdmin())
            {!! Form::text('email') !!}</p>

            <div class="row">
                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-5">
                    <strong>Select Roles:</strong><br />
                    @foreach ($roles as $role)
                        <input name="{{ str_replace(' ','_',$role->name) }}" type="checkbox"
                             {{ isset($user) && $user->hasRole($role->name) ? 'checked="checked"' : '' }}>
                        <label  for="{{ str_replace(' ','_',$role->name) }}">{{ $role->name }}</label><br />
                    @endforeach
                </div>
                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-5">
                    <strong>Select Instruments:</strong><br />
                    @foreach ($instruments as $instrument)
                        <input name="{{ str_replace(' ','_',$instrument->name) }}" type="checkbox"
                             {{ isset($user) && $user->hasInstrument($instrument->name) ? 'checked="checked"' : '' }}>
                        <label  for="{{ str_replace(' ','_',$instrument->name) }}">{{ $instrument->name }}</label><br />
                    @endforeach
                </div>
            </div>
        @else
            {!! Form::hidden('email') !!}
            {{ isset($user) ? $user->email : '' }}
            <br />
            <br />
            <label>Your Roles:</label>
            @foreach ($user->roles as $key => $role)
                {{ ucfirst($role->name) }}{{ $key+1<$user->roles->count() ? ',' : '' }}
            @endforeach
        @endif


    @if (isset($user))
        <p>{!! Form::submit('Update'); !!}</p>
        @if (Auth::user()->isAdmin())
            <hr>
            <a class="btn btn-danger btn-sm"  role="button" href="{{ url('admin/users/'. $user->id) }}/delete">
                <i class="fa fa-trash" > </i> &nbsp; Delete
            </a>
        @endif
    @else
        {!! Form::submit('Submit'); !!}
    @endif

    <a href="#" onclick="history.back()">{!! Form::button('Cancel') !!}</a>
    {!! Form::close() !!}

    <script type="text/javascript">document.forms.inputForm.first_name.focus()</script>
    
@stop

