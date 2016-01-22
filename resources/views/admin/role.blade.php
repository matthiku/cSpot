@extends('layouts.main')

@section('title', "Create or Update a User Role")

@section('roles', 'active')



@section('content')

    @include('layouts.sidebar')

    @include('layouts.flashing')


    @if (isset($role))
        <h2>Update Role</h2>
        {!! Form::model( $role, array('route' => array('admin.roles.update', $role->id), 'method' => 'put') ) !!}
    @else
        <h2>Create Role</h2>
        {!! Form::open(array('action' => 'Admin\RoleController@store')) !!}
    @endif
        <p>{!! Form::label('name', 'Role Name'); !!}<br>
           {!! Form::text('name'); !!}</p>

    @if (isset($role))
        <p>{!! Form::submit('Update'); !!}</p>
        <hr>
        <a class="btn btn-danger btn-sm"  role="button" href="/admin/roles/{{ $role->id }}/delete">
            <i class="fa fa-trash" > </i> &nbsp; Delete
        </a>
    @else
        <p>{!! Form::submit('Submit'); !!}
    @endif

    <a href="/admin/roles">{!! Form::button('Cancel'); !!}</a></p>
    {!! Form::close() !!}
    
@stop