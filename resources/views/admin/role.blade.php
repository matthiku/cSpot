
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Create or Update a User Role")

@section('roles', 'active')



@section('content')


    @include('layouts.flashing')


    @if (isset($role))
        <h2>Update Role</h2>
        {!! Form::model( $role, array('route' => array('roles.update', $role->id), 'method' => 'put', 'id' => 'inputForm') ) !!}
    @else
        <h2>Create Role</h2>
        {!! Form::open(array('action' => 'Admin\RoleController@store', 'id' => 'inputForm') ) !!}
    @endif
        <p>{!! Form::label('name', 'Role Name'); !!} <i class="red">*</i><br>
           {!! Form::text('name'); !!}</p>

    @if (isset($role))
        <p>{!! Form::submit('Update', ['class'=>'btn btn-primary']); !!}</p>
        <hr>
        <a class="btn btn-danger"  href="{{ url('admin/roles/'.$role->id) }}/delete">
            <i class="fa fa-trash" > </i> &nbsp; Delete
        </a>
    @else
        <p>{!! Form::submit('Submit', ['class'=>'btn btn-primary']); !!}
    @endif

    <script type="text/javascript">document.forms.inputForm.name.focus()</script>

    <a href="{{ url('admin/roles/') }}">{!! Form::button('Cancel', ['class'=>'btn btn-secondary']); !!}</a></p>
    {!! Form::close() !!}

    <span><i class="red">*</i> = mandatory field(s) &nbsp;</span>
    
@stop