@extends('layouts.main')

@section('title', "Create or Update a User")




@section('content')

    @include('layouts.sidebar')

    @include('layouts.flashing')


    @if (isset($user))
        <h2>Update User</h2>
        {!! Form::model( $user, array('route' => array('admin.users.update', $user->id), 'method' => 'put', 'id' => 'inputForm') ) !!}
    @else
        <h2>Create User</h2>
        {!! Form::open(array('action' => 'Admin\UserController@store', 'id' => 'inputForm')) !!}
    @endif

        <p>{!! Form::label('first_name', 'First Name'); !!}<br>
           {!! Form::text('first_name'); !!}</p>
        <p>{!! Form::label('last_name', 'Last Name'); !!}<br>
           {!! Form::text('last_name'); !!}</p>
        <p>{!! Form::label('email', 'Email Address'); !!}<br>
           {!! Form::text('email'); !!}</p>
        <strong>Select Roles:</strong><br />
        @foreach ($roles as $role)
            <input name="{{ $role->name }}" type="checkbox"
                 {{ isset($user) && $user->hasRole($role->name) ? 'checked="checked"' : '' }}>
            <label  for="{{ $role->name }}">{{ $role->name }}</label><br />
        @endforeach


    @if (isset($user))
        <p>{!! Form::submit('Update'); !!}</p>
        <hr>
        <a class="btn btn-danger btn-sm"  role="button" href="/admin/users/{{ $user->id }}/delete">
            <i class="fa fa-trash" > </i> &nbsp; Delete
        </a>
    @else
        <p>{!! Form::submit('Submit'); !!}
    @endif

    <script type="text/javascript">document.forms.inputForm.first_name.focus()</script>

    <a href="/admin/users">{!! Form::button('Cancel'); !!}</a></p>
    {!! Form::close() !!}
    
@stop

