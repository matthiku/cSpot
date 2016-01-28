@extends('layouts.main')

@section('title', "Create or Update Plan Item")

@section('plan', 'active')



@section('content')


    @include('layouts.flashing')

    @if (isset($item))
        {!! Form::model( $item, array(
            'route'  => array('cspot.items.update', $item->id), 
            'method' => 'put', 
            'id'     => 'inputForm',
            'class'  => 'form-horizontal'
            )) !!}
    @else
        {!! Form::open(array('action' => 'Cspot\ItemController@store', 'id' => 'inputForm')) !!}
    @endif

    {!! Form::hidden('seq_no', $seq_no) !!}
    {!! Form::hidden('plan_id', isset($plan) ? $plan->id : $item->plan_id ) !!}


    <div class="row">
        @if (isset($item))
            <div class="col-sm-6">
                <h2>Update Item No {{$seq_no}}</h2>
            </div>
            <div class="col-sm-6 text-xs-right">

                {!! Form::submit('Save changes'); !!}

                @if (Auth::user()->isAdmin())
                    <a class="btn btn-danger btn-sm"  item="button" href="/cspot/items/{{ $item->id }}/delete">
                        <i class="fa fa-trash" > </i> &nbsp; Delete
                    </a>
                @endif
                <a href="/cspot/plans/{{$item->plan_id}}/edit">{!! Form::button('Cancel - Back to Plan'); !!}</a>
        @else
            <div class="col-sm-6">
                <h2>Add Item</h2>
                <h5>to the Service plan (id {{ $plan->id }}) for {{ $plan->date->formatLocalized('%A, %d %B %Y') }}</h5>
        @endif
            </div>
    </div>


    <div class="row">

        <div class="col-lg-3 col-md-6">
            <br>
            <div class="row form-group">
                <div class="col-xs-12">
                    {!! Form::label('comment', 'Comment or Bible Ref.'); !!}
                    <p>
                        {!! Form::text('comment'); !!}
                        @if ($errors->has('comment'))
                            <br><span class="help-block">
                                <strong>{{ $errors->first('comment') }}</strong>
                            </span>
                        @endif
                    </p>
                </div>        
                <div class="col-xs-12">
                    {!! Form::label('version', 'Version'); !!}
                    <p>
                        {!! Form::text('version'); !!}
                        @if ($errors->has('version'))
                            <br><span class="help-block">
                                <strong>{{ $errors->first('version') }}</strong>
                            </span>
                        @endif
                    </p>
                </div>            
                <div class="col-xs-12">
                    {!! Form::label('key', 'Key'); !!}
                    <p>{!! Form::text('key'); !!}</p>
                </div>
            </div>
        </div>

        @if ( isset($item->song->id) && $item->song_id<>0 )

            {!! Form::hidden('song_id', $item->song_id) !!}

            <div class  = "col-lg-3 col-md-6">
                <br>
                <div  class="row form-group link" 
                    onclick="location.href='/cspot/plans/{{$item->plan_id}}/items/{{$item->id}}/song/edit'"
                      title="Click to edit">
                    <h4>Song Number</h4>
                    <p>{{ $item->song->book_ref }}</p>

                    <h4>Song Title</h4>
                    <p>
                        {{ isset($item->song->title) ? $item->song->title : '' }}
                        {{ isset($item->song->title_2) ? ' ('. $item->song->title_2 .')' : '' }}
                    </p>
                </div>
                <div class="row form-group">
                    <h5><a target="new" href="https://www.youtube.com/watch?v={{ $item->song->youtube_id }}">
                                        <i class="fa fa-youtube"></i> Play on Youtube</a></h5>
                </div>
                <div class="row form-group">
                    To search for another song,
                    {!! Form::label('search', 'enter song number, title or author or parts thereof:') !!}
                    {!! Form::text('search') !!}
                    <input type="submit" name="searchBtn" value="Search" />
                    @if ($errors->has('search'))
                        <br><span class="help-block">
                            <strong>{{ $errors->first('search') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="col-lg-6 col-md-12">
                <div class="row form-group">
                    <h4>Lyrics</h4>
                    <pre>{{ $item->song->lyrics }}</pre>
                </div>
            </div>

        @else
            <br><br>
            <div class="col-lg-6 col-md-12">
                @if ( Session::has('songs'))
                    Select a song:
                    <div class="c-inputs-stacked">
                        @foreach (Session::get('songs') as $song)
                            <label class="c-input c-radio" title="{{$song->lyrics}}">
                                <input value="{{$song->id}}" name="song_id" type="radio">
                                <span class="c-indicator"></span>
                                {{$song->book_ref}}, 
                                {{$song->title}}{{ $song->title_2 ? ' ('. $song->title_2 .')' : '' }},
                                {{$song->author}}
                            </label>
                        @endforeach
                    </div>
                    Or search for another song -
                @else
                    To search for a song,
                @endif
                {!! Form::label('search', 'enter song number, title or author or parts thereof:') !!}<br/>
                {!! Form::text('search') !!}
                <input type="submit" name="searchBtn" value="Search" />
                @if ($errors->has('search'))
                    <br><span class="help-block">
                        <strong>{{ $errors->first('search') }}</strong>
                    </span>
                @endif
            </div>
        @endif

    </div>

    @if (! isset($item))
        <!-- See if user wants to add more items to this plan -->
        <input type="hidden" name="moreItems" value="false">
        <div class="checkbox">
          <label>
            <input checked="checked" type="checkbox" value="Y" name="moreItems">
            Tick to add another item to this plan after saving this one
          </label>
        </div>                        
        {!! Form::submit('Submit'); !!}
        <a href="/cspot/plans/{{isset($plan) ? $plan->id : $plan_id}}/edit">{!! Form::button('Cancel - Back to Plan'); !!}</a>
    @endif

    {!! Form::close() !!}


    <script type="text/javascript">document.forms.inputForm.comment.focus()</script>

    
@stop