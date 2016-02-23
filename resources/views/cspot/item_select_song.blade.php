
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Create or Update Plan Item")

@section('plan', 'active')



@section('content')


    @include('layouts.flashing')

    {!! Form::open(array('action' => 'Cspot\ItemController@store', 'id' => 'inputForm')) !!}

    {!! Form::hidden('plan_id', $plan_id) !!}
    {!! Form::hidden('item_id', $item_id) !!}

    <div class="row">
        <div class="col-md-6">
            <h2>Select a Song</h2>
        </div>
    </div>


    <hr>


    <div class="row">
        <div class="col-lg-6">


            <div class="c-inputs-stacked c-select-compact">

                @foreach ($songs as $song)
                    <label class="c-input c-radio c-radio-label" title="{{ $song->lyrics }}" data-toggle="tooltip">

                        <input value="{{ $song->id }}" name="song_id" type="radio">

                        <span class="c-indicator"></span>
                        {{ $song->book_ref ? $song->book_ref.',' : '' }}
                        {{ $song->title }}
                        {{ $song->title_2 ? '('. $song->title_2 .')' : '' }}
                        <br>

                        <small class="c-radio-small">
                            @if ( $song->items->count() )
                                (used {{ $song->items->count() }} times, last on 
                                {{ $song->lastPlanUsingThisSong()->date->formatLocalized('%d-%b-%y') }})
                            @else
                                (never used)
                            @endif
                        </small>

                    </label>
                @endforeach

            </div>

            <input type="submit" name="submit" value="Submit" />
            <br>


            Or search for still another song. Just
            <div class="song-search">
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
    </div>

    {!! Form::close() !!}

    
@stop