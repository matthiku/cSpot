
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('setup', 'active')




@section('content')


    @include('layouts.flashing')

    
    <h2>{{ $heading }}</h2>


    @if (isset($bibleversion))
        {!! Form::model( $bibleversion, array('route' => array('bibleversions.update', $bibleversion->id), 'method' => 'put', 'id' => 'inputForm', 'oninput' => 'enableSubmitButton()') ) !!}

    @else
        {!! Form::open(array('action' => 'Admin\BibleversionController@store', 'id' => 'inputForm', 'oninput' => 'enableSubmitButton()') ) !!}

    @endif



    <p>{!! Form::label('name', 'Bible Version Name'); !!} <i class="red">*</i><br>
       {!! Form::text('name'); !!}</p>


    <p>{!! Form::label('copyright', 'Copyright'); !!} <i class="red">*</i><br>
       {!! Form::textarea('copyright'); !!}</p>



    @if (isset($bibleversion))
        <p>{!! Form::submit('Update', ['class'=>'btn btn-outline-success submit-button disabled']); !!}</p>
        <hr>
        <a class="btn btn-danger"  bibleversion="button" href="{{ url('admin/bibleversions/'.$bibleversion->id) }}/delete">
            <i class="fa fa-trash" > </i> &nbsp; Delete
        </a>

    @else
        <p>{!! Form::submit('Submit', ['class'=>'btn btn-outline-success submit-button disabled']); !!}

    @endif


    <script type="text/javascript">document.forms.inputForm.name.focus()</script>


    <a href="{{ url('admin/bibleversions/') }}">{!! Form::button('Cancel', ['class'=>'btn btn-secondary cancel-button']); !!}</a></p>

    {!! Form::close() !!}
    

    <span><i class="red">*</i> = mandatory field(s) &nbsp;</span>
    
@stop