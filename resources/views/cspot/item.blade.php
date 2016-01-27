@extends('layouts.main')

@section('title', "Create or Update Plan Item")

@section('plan', 'active')



@section('content')


    @include('layouts.flashing')


    @if (isset($item))
        <h2>Update Item No {{$item->seq_no}}</h2>
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

        {!! Form::hidden('plan_id', isset($plan) ? $plan->id : $plan_id ) !!}
        {!! Form::hidden('seq_no', $seq_no) !!}
    @endif

        <div class="row form-group">
            <div class="col-sm-4">
                {!! Form::label('song_id', 'Song ID'); !!}
            </div>
            <div class="col-sm-8">
                {!! Form::text('song_id'); !!}
                @if ($errors->has('song_id'))
                    <br><span class="help-block"><strong>{{ $errors->first('song_id') }}</strong></span>
                @endif
            </div>
        </div>

        <div class="row form-group">
            {!! Form::label('title', 'Song Title', ['class' => 'col-sm-4']); !!}
            <div class="col-sm-8">{!! Form::text('title'); !!}</div>
        </div>

        <div class="row form-group">
            {!! Form::label('comment', 'Comment', ['class' => 'col-sm-4']); !!}
            <div class="col-sm-8">
                {!! Form::text('comment'); !!}
                @if ($errors->has('comment'))
                    <br><span class="help-block">
                        <strong>{{ $errors->first('comment') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="row form-group">
            {!! Form::label('version', 'Version', ['class' => 'col-sm-4']); !!}
            <div class="col-sm-8">
                {!! Form::text('version'); !!}
                @if ($errors->has('version'))
                    <br><span class="help-block">
                        <strong>{{ $errors->first('version') }}</strong>
                    </span>
                @endif
            </div>
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

            <!-- See if user wants to add more items to this plan -->
            <input type="hidden" name="moreItems" value="false">
            <div class="checkbox">
              <label>
                <input checked="checked" type="checkbox" value="Y" name="moreItems">
                Tick to add another item to this plan after saving this one
              </label>
            </div>                
            
            {!! Form::submit('Submit'); !!}

        @endif

        <a href="/cspot/plans/{{isset($plan) ? $plan->id : $plan_id}}/edit">{!! Form::button('Cancel - Back to Plan'); !!}</a>

    {!! Form::close() !!}


    <script type="text/javascript">document.forms.inputForm.seq_no.focus()</script>

    
@stop