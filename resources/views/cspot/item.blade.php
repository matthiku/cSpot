@extends('layouts.main')

@section('title', "Create or Update Plan Item")

@section('plan', 'active')



@section('content')


    @include('layouts.flashing')


    @if (isset($item))
        <h2>Update a Item</h2>
        {!! Form::model( $item, array(
            'route'  => array('cspot.items.update', $item->id), 
            'method' => 'put', 
            'id'     => 'inputForm',
            'class'  => 'form-horizontal'
            )) !!}
    @else
        <h2>Add Item</h2>
        <h5>to the Service plan (id {{ $plan->id }}) for {{ $plan->date->formatLocalized('%A, %d %B %Y') }}</h5>
        {!! Form::open(array('action' => 'Cspot\ItemController@store', 'id' => 'inputForm')) !!}
    @endif

        {!! Form::hidden('plan_id', $plan->id) !!}
    
        <div class="row form-group">
           {!! Form::label('seq_no', 'Item No.', ['class' => 'col-sm-4']); !!}
           <div class="col-sm-8">{!! Form::number('seq_no'); !!}</div>
           <script type="text/javascript">
                document.getElementById("seq_no").setAttribute('step','0.1');
                document.getElementById("seq_no").setAttribute('min','0.1');
            </script>
        </div>
        <div class="row form-group">
            {!! Form::label('song_id', 'Song ID', ['class' => 'col-sm-4']); !!}
            <div class="col-sm-8">{!! Form::text('song_id'); !!}</div>
        </div>
        <div class="row form-group">
            {!! Form::label('title', 'Song Title', ['class' => 'col-sm-4']); !!}
            <div class="col-sm-8">{!! Form::text('title'); !!}</div>
        </div>
        <div class="row form-group">
            {!! Form::label('comment', 'Comment', ['class' => 'col-sm-4']); !!}
            <div class="col-sm-8">{!! Form::text('comment'); !!}</div>
        </div>
        <div class="row form-group">
            {!! Form::label('version', 'Version', ['class' => 'col-sm-4']); !!}
            <div class="col-sm-8">{!! Form::text('version'); !!}</div>
        </div>
        <div class="row form-group">
            {!! Form::label('key', 'Key', ['class' => 'col-sm-4']); !!}
            <div class="col-sm-8">{!! Form::text('key'); !!}</div>
        </div>


        @if (isset($item))
            {!! Form::submit('Save changes'); !!}

            @if (Auth::user()->isAdmin())
                <a class="btn btn-danger btn-sm"  item="button" href="/cspot/items/{{ $item->id }}/delete">
                    <i class="fa fa-trash" > </i> &nbsp; Delete
                </a>
            @endif
        @else
            {!! Form::submit('Submit'); !!}
        @endif
        <a href="#" onclick="history.go(-1)">{!! Form::button('Cancel'); !!}</a>

    {!! Form::close() !!}


    <script type="text/javascript">document.forms.inputForm.seq_no.focus()</script>

    
@stop