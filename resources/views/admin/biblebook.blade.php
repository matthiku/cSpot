
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('setup', 'active')




@section('content')


    @include('layouts.flashing')

    
    <h2>{{ $heading }}</h2>


    @if (isset($biblebook))
        {!! Form::model( $biblebook, array('route' => array('biblebooks.update', $biblebook->id), 'method' => 'put', 'id' => 'inputForm', 'oninput' => 'enableSubmitButton()') ) !!}

    @else
        {!! Form::open(array('action' => 'Admin\BiblebookController@store', 'id' => 'inputForm', 'oninput' => 'enableSubmitButton()') ) !!}

    @endif



    <p>{!! Form::label('name', 'Bible Version Name'); !!} <i class="red">*</i><br>
       {!! Form::text('name'); !!}</p>



    @if (isset($biblebook))
        <p>{!! Form::submit('Update', ['class'=>'btn btn-outline-success submit-button disabled']); !!}</p>
        <hr>
        <a class="btn btn-danger"  biblebook="button" href="{{ url('admin/biblebooks/'.$biblebook->id) }}/delete">
            <i class="fa fa-trash" > </i> &nbsp; Delete
        </a>

    @else
        <p>{!! Form::submit('Submit', ['class'=>'btn btn-outline-success submit-button disabled']); !!}

    @endif


    <script type="text/javascript">document.forms.inputForm.name.focus()</script>


    <a href="{{ url('admin/biblebooks/') }}">{!! Form::button('Cancel', ['class'=>'btn btn-secondary cancel-button']); !!}</a></p>

    {!! Form::close() !!}
    

    <span><i class="red">*</i> = mandatory field(s) &nbsp;</span>
    
@stop