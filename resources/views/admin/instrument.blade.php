
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Create or Update a User Instrument")

@section('setup', 'active')



@section('content')


    @include('layouts.flashing')


    @if (isset($instrument))
        <h2>Update Instrument</h2>
        {!! Form::model( $instrument, array('route' => array('instruments.update', $instrument->id), 'method' => 'put', 'id' => 'inputForm') ) !!}
    @else
        <h2>Create Instrument</h2>
        {!! Form::open(array('action' => 'Admin\InstrumentController@store', 'id' => 'inputForm') ) !!}
    @endif
        <p>{!! Form::label('name', 'Instrument Name'); !!} <i class="red">*</i><br>
           {!! Form::text('name'); !!}</p>

    @if (isset($instrument))
        <p>{!! Form::submit('Update', ['class'=>'btn btn-primary']); !!}</p>
        <hr>
        <a class="btn btn-danger"  instrument="button" href="{{ url('admin/instruments/'.$instrument->id) }}/delete">
            <i class="fa fa-trash" > </i> &nbsp; Delete
        </a>
    @else
        <p>{!! Form::submit('Submit', ['class'=>'btn btn-primary']); !!}
    @endif

    <script type="text/javascript">document.forms.inputForm.name.focus()</script>

    <a href="{{ url('admin/instruments/') }}">{!! Form::button('Cancel', ['class'=>'btn btn-secondary']); !!}</a></p>
    {!! Form::close() !!}
    
    <span><i class="red">*</i> = mandatory field(s) &nbsp;</span>
    
@stop