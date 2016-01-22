@extends('layouts.main')

@section('title', "Create or Update a User Type")

@section('types', 'active')



@section('content')

    @include('layouts.sidebar')

    @include('layouts.flashing')


    @if (isset($type))
        <h2>Update Type</h2>
        {!! Form::model( $type, array('route' => array('admin.types.update', $type->id), 'method' => 'put') ) !!}
    @else
        <h2>Create Type</h2>
        {!! Form::open(array('action' => 'Admin\TypeController@store')) !!}
    @endif
        <p>{!! Form::label('name', 'Type Name'); !!}<br>
           {!! Form::text('name'); !!}</p>

    @if (isset($type))
        <p>{!! Form::submit('Update'); !!}</p>
        <hr>
        <a class="btn btn-danger btn-sm"  type="button" href="/admin/types/{{ $type->id }}/delete">
            <i class="fa fa-trash" > </i> &nbsp; Delete
        </a>
    @else
        <p>{!! Form::submit('Submit'); !!}
    @endif

    <a href="/admin/types">{!! Form::button('Cancel'); !!}</a></p>
    {!! Form::close() !!}
    
@stop