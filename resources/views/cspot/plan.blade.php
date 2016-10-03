
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

<?php Use Carbon\Carbon; ?>

@extends('layouts.main')

@section('title', "Event Planning")

@section('plans', 'active')


@if (session()->has('defaultValues'))
    <?php $defaultValues = session('defaultValues') ?>
@endif

@section('content')


    @include('layouts.flashing')


    {{-- html input form definition 
    --}}
    @if (isset($plan))
        @if (Auth::user()->isEditor())
            {!! Form::model( $plan, array(
                'route'  => array('plans.update', $plan->id), 
                'method' => 'put', 
                'file'   => 'true',
                'id'     => 'inputForm',
                'class'  => 'form-horizontal'
                )) !!}
        @else
            {!! Form::model( $plan, array(
                'route'  => array('addNote', $plan->id), 
                'method' => 'put', 
                'id'     => 'inputForm',
                'class'  => 'form-horizontal'
                )) !!}
        @endif
    @else
        {!! Form::open(array('action' => 'Cspot\PlanController@store', 'id' => 'inputForm')) !!}
    @endif



    <!-- 
        page header 
    -->
    <div class="row">
        <div class="col-md-9 col-xl-8 md-center">

            @if ( isset($plan) && $plan->items()->count() )

                {{-- show links to various plan presentation modes 
                --}}
                <div class="dont-print">
                    <div class="pull-xs-right">
                        <a title="Show sheetmusic (if available) for the songs on this plan"
                            onclick="$('#show-spinner').modal({keyboard: false});" 
                            href="{{ url('cspot/items/'.$plan->firstItem()->id.'/sheetmusic/') }}">
                            <i class="fa fa-music">&nbsp;</i>Sheetmusic</a>
                    </div>
                    <div class="pull-xs-right m-r-1">
                        <a title="Show guitar chords (if available) for the songs on this plan" 
                            onclick="$('#show-spinner').modal({keyboard: false});" 
                            href="{{ url('cspot/items/').'/'.$plan->firstItem()->id }}/chords">
                            <i class="fa fa-file-code-o">&nbsp;</i>Chords</a>
                    </div>
                    <div class="pull-xs-right m-r-1">
                        <a title="Start projector-enabled presentation of each song and scripture reading in this plan" 
                            onclick="$('#show-spinner').modal({keyboard: false});" 
                            href="{{ url('cspot/items/'.$plan->firstItem()->id.'/present/') }}">
                            <i class="fa fa-tv">&nbsp;</i>Present</a>
                    </div>
                    <div class="pull-xs-right m-r-1">
                        <a title="YouTube playlist of all songs" target="new" 
                            href="{{ env('YOUTUBE_PLAYLIST_URL', 'https://www.youtube.com/playlist?list=').env('CHURCH_YOUTUBE_PLAYLIST_ID', '') }}">
                            <i class="fa fa-youtube">&nbsp;</i>play all</a>
                    </div>
                </div>
            @endif
            @if ( isset($plan) )

                <h4 class="hidden-md-down">Plan for "{{ $plan->type->name }}" on {{ $plan->date->formatLocalized('%A, %d %B %Y') }}</h4>
                <h4 class="hidden-lg-up">"{{ $plan->type->name }}" on {{ $plan->date->formatLocalized('%a, %d %B') }}</h4>
                <small class="hidden-lg-down">{{ $plan->subtitle }}</small>


            @else

                <h3>Add Event Plan</h3>

            @endif

        </div>




        <div class="col-md-3 col-xl-4 right md-center">

            @if (isset($plan))
                <div class="pull-xs-left plan-details">
                    <big>
                        L.:&nbsp;<strong>{{ $plan->leader ? $plan->leader->name : $plan->leader_id }}</strong> &nbsp;
                        @if ( strtoupper($plan->teacher->name)<>'N/A' )
                            T.:&nbsp;<strong>{{ $plan->teacher->name }}</strong>
                        @endif
                        <?php
                            $teamList = ''; // create the list of team members and their roles for this plan
                            foreach ( $plan->teams as $key => $team ) {
                                if ($team->user) {
                                    $teamList .= $team->user->name . ' as ';
                                    $teamList .= $team->role ? ucfirst($team->role->name) : '(tbd)';
                                    $teamList .= $team->confirmed ? ' (confirmed)' : ' (unconfirmed)';
                                    if ($key+1 < $plan->teams->count())
                                        $teamList .= ",\n";
                                }
                            }
                        ?>
                        <a href="{{ url('cspot/plans/'.$plan->id.'/team') }}" class="m-l-2 nowrap" 
                            onclick="$('#show-spinner').modal({keyboard: false});" 
                            data-template='<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><pre class="tooltip-inner tooltip-medium"></pre></div>'
                            data-placement="bottom" data-toggle="tooltip" title="{{ $teamList }}">
                            <i class="fa fa-users"></i>&nbsp;Team<small>({{$plan->teams->count()}})</small>
                        </a> 
                    </big>
                        <?php
                            $resrcList = ''; // create the list of team members and their roles for this plan
                            foreach ( $plan->resources as $key => $resrc ) {
                                $resrcList .= $resrc->name; 
                                //$resrcList .= ' ('+$resrc->pivot->comment+')'; 
                                if ($key+1 < $plan->resources->count())
                                    $resrcList .= ",\n";
                            }
                        ?>
                        <a href="{{ url('cspot/plans/'.$plan->id.'/resource') }}" class="m-l-2 nowrap" 
                            onclick="$('#show-spinner').modal({keyboard: false});" title="{{ $resrcList }}">
                            <i class="fa fa-cubes"></i>&nbsp;Resources<small>({{$plan->resources->count()}})</small>
                        </a> 
                </div>
            @endif


            @if ( Auth::user()->isEditor() && isset($plan) && $plan->date >= \Carbon\Carbon::yesterday() ) 
                <div class="pull-xs-right small">
                    &nbsp; <a href="#" onclick="$('.plan-details').toggle()">edit plan details</a>
                </div>
                <div class="pull-xs-right plan-details small" style="display: none;">
                    (last changed by {{ $plan->changer }} {{ Carbon::now()->diffForHumans( $plan->updated_at, true ) }} ago)
                </div>
            @endif



            <div class="form-buttons">
                <big>
                    @if (isset($plan))
                        @if (Auth::user()->isEditor())
                            <span class="has-warning">
                            {!! Form::submit('Save changes', [
                                'data-toggle'    => 'tooltip', 
                                'data-placement' => 'left',
                                'class'          => 'form-submit plan-details text-help',
                                'style'          => 'display: none',
                                'title'          => 'Click to save changes to notes, event type, date, leader or teacher',
                            ]); !!}</span>
                        @endif

                    @else
                        <input class="xs-width-half" type="submit" value="Submit">
                    @endif
                </big>
            </div>  

        </div>
    </div>


    <div class="plan-details row center"{!! isset($plan) ? " style='display: none'" : '' !!}>


        @if (Auth::user()->isEditor())
            <div class="col-xl-4 col-lg-6">
                <div class="form-group">
                    <select name="type_id" id="type_id" class="form-control text-help plan-form-minw c-select" 
                        onchange="fillPlanDefaultValues(this)">
                        @if (! isset($plan) && ! isset($defaultValues['type_id'] ))
                            <option selected>
                                Select event type...
                            </option>
                        @endif
                        @foreach ($types as $type)
                            <option 
                                @if( ( ''<>old('type_id') && $type->id==old('type_id') )  ||  isset($plan) && $plan->type_id==$type->id  ||  isset($defaultValues['type_id']) && $defaultValues['type_id']==$type->id )
                                    selected
                                @endif
                                value="{{ $type->id }}">{{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('type_id'))
                        <br><span class="help-block">
                            <strong>{{ $errors->first('type_id') }}</strong>
                        </span>
                    @endif
                    <span class="pull-xs-left">Subtitle: {!! Form::text('subtitle') !!}</span><br>
                    <small class="pull-xs-left text-muted">(Used in the Announcements slide - e.g. a location!)</small>
                </div>
            </div>

        
            <div class="col-xl-3 col-lg-6">
                <div class="row form-group">
                    @if ( isset($plan) )
                        {!! Form::date( 
                            'date', $plan->date, 
                            ['class'    => 'plan-form-minw center', 'onchange' => 'enableSaveButton(this)' ] ) 
                        !!}
                    @elseif (isset($defaultValues))
                        {!! Form::date( 
                            'date', $defaultValues['date'], 
                            ['class' => 'plan-form-minw center', 'onchange' => 'enableSaveButton(this)' ] )
                        !!}
                    @else
                        {!! Form::date( 
                            'date', \Carbon\Carbon::now(), 
                            ['class' => 'plan-form-minw center', 'onchange' => 'enableSaveButton(this)' ] )
                        !!}
                    @endif

                    <div class="form-group" id="editPlanServiceTimes">

                        {!! Form::label('start', 'Event from:'); !!}
                        @if (isset($defaultValues))
                            {!! Form::time( 'start', $defaultValues['start']); !!}
                        @else
                            {!! Form::time( 'start'); !!}
                        @endif

                        {!! Form::label('end', ' to '); !!}
                        @if (isset($defaultValues))
                            {!! Form::time( 'end', $defaultValues['end']); !!}
                        @else
                            {!! Form::time( 'end'); !!}
                        @endif

                    </div>

                    <script>
                        $($('#editPlanServiceTimes').children('input')[0]).attr('onchange', 'enableSaveButton(this)');
                        $($('#editPlanServiceTimes').children('input')[1]).attr('onchange', 'enableSaveButton(this)');
                    </script>
                </div>
            </div>                    
        @endif


        <div class="col-xl-5 col-lg-12">
            <div class="col-sm-6">
                <div class="row form-group nowrap" style="max-width: 150px;">
                    <label class="form-control-label">Leader </label>
                    <select name="leader_id" id="leader_id" class="form-control text-help c-select" onchange="enableSaveButton(this)"
                            {{ Auth::user()->isEditor() ? '' : ' disabled' }}>
                        @if (! isset($plan) && ! isset($defaultValues['leader_id']) )
                            <option selected>
                                Select ...
                            </option>
                        @endif
                        @foreach ($users as $user)
                            @if( $user->hasRole('leader'))
                                <option 
                                    @if(   ( ''<>old('leader_id') && $user->id==old('leader_id') )  
                                        || ( isset($plan) && $plan->leader_id==$user->id ) 
                                        || ( isset($defaultValues['leader_id']) && $defaultValues['leader_id']==$user->id ) )
                                            selected
                                    @endif
                                    value="{{ $user->id }}">{{ $user->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    @if ($errors->has('leader_id'))
                        <br><span class="help-block">
                            <strong>{{ $errors->first('leader_id') }}</strong>
                        </span>
                    @endif
                    @if ( isset($plan) && $plan->isFuture() && Auth::user()->isAuthor() )
                        <a href="{{ url('cspot/plans/'.$plan->id.'/remind/'.$plan->leader_id) }}" 
                           class="btn btn-sm btn-secondary" role="button"
                           data-toggle="tooltip" title="Send reminder to leader to insert missing items">
                            <i class="fa fa-envelope"></i></a>
                    @endif
                </div>
            </div>          


            <div class="col-sm-6">
                <div class="row form-group nowrap">
                    <label class="form-control-label">Teacher
                    <select name="teacher_id" class="form-control text-help c-select" onchange="enableSaveButton(this)"
                            {{ Auth::user()->isEditor() ? '' : ' disabled' }}>
                        @if (! isset($plan))
                            <option>
                                Select ...
                            </option>
                            <option selected value="0">None</option>
                        @endif
                        @foreach ($users as $user)
                            @if( $user->hasRole('teacher'))
                                <option 
                                    @if(   ( ''<>old('teacher_id') && $user->id==old('teacher_id') )  
                                        || ( isset($plan) && $plan->teacher_id==$user->id ) )
                                        selected
                                    @endif
                                    value="{{ $user->id }}">{{ $user->name }}</option>
                            @endif
                        @endforeach
                    </select>
                    @if ( ! isset($plan) || (isset($plan) && Auth::user()->ownsPlan($plan->id)) )
                        <big>
                            <a tabindex="0" href="#"
                                data-container="body" data-toggle="tooltip"
                                title="Select 'none' if the leader is also the teacher">
                                <i class="fa fa-question-circle"></i></a>
                        </big>
                    @endif
                    @if ($errors->has('teacher_id'))
                        <br><span class="help-block">
                            <strong>{{ $errors->first('teacher_id') }}</strong>
                        </span>
                    @endif
                    @if ( isset($plan) && $plan->isFuture()  &&  Auth::user()->ownsPlan($plan->id) )
                        <a href="{{ url('cspot/plans/'.$plan->id.'/remind/'.$plan->teacher_id) }}" 
                           class="btn btn-sm btn-secondary" role="button" data-placement="left"
                           data-toggle="tooltip" title="Send reminder to teacher to insert missing items">
                            <i class="fa fa-envelope"></i></a>
                    @endif
                    </label>
                </div>
            </div>
        </div>                    

    </div>




    <!-- 
        Show items for existing plan 
        ____________________________
    -->
    @if (isset($plan))

        @include('cspot.items')

        @if (Auth::user()->ownsPlan($plan->id) && $plan->isFuture() )
            <a href="{{ url('cspot/songs?plan_id='.$plan->id) }}"  onclick="showSpinner()"
                title="Search for a song via the full song listing" 
                class="btn btn-sm btn-info pull-xs-right">
                    <i class="fa fa-plus"></i><i class="fa fa-music"></i>&nbsp; - Search and add song</a>
        @endif

    @else

        <!-- Checkbox to add default items into NEW plan -->
        <input type="hidden" name="defaultItems" value="false">
        <div class="checkbox center">
            <label>
                <input checked="checked" type="checkbox" value="Y" name="defaultItems">
                Insert default items for this plan?
            </label>
        </div>    
        <!-- Checkbox to add default TIMES into NEW plan -->
        <input type="hidden" name="defaultValues" value="false">
        <div class="checkbox center">
            <label>
                <input checked="checked" type="checkbox" value="Y" name="defaultValues"
                    onclick="$('#planServiceTimes').toggle()">
                Insert other default values (times, resources) for this plan?
                <br><small class="text-muted">(see: <a href="{{ url('admin/types') }}">List of event types</a>)</small>
            </label>
            <div class="center" id="planServiceTimes" style="display: none">
                {!! Form::label('start', 'New times:'); !!}
                {!! Form::time( 'start'); !!}
                {!! Form::label('end', ' - '); !!}
                {!! Form::time( 'end');   !!}      
            </div>
        </div>

        <!-- what to do after creating this plan? Either go to the new plan or add another one of this type -->
        <input type="hidden" name="addAnother" value="false">
        <div class="checkbox center">
            <label>
                <input 
                {{ isset($defaultValues) ? 'checked="checked"' : '' }}
                type="checkbox" value="Y" name="addAnother">
                Add another Event Plan after this one?
            </label>
        </div>                

    @endif





    <div style="clear:both" class="form-group
        @if (! isset($plan))
            center
        @endif
        ">
        @if (Auth::user()->isEditor())
            {!! Form::label('info', 'Notes:', ['class' => 'form-control-label', 'onclick'=>'showSpinner()']); !!}
            <br/>
            {!! Form::textarea('info') !!}
            <script>
                document.forms.inputForm.info.rows=5;
                $('#info').attr('onchange',"enableSaveButton(this)");
            </script>
        @else
            @if (isset($plan) && $plan->info)
                <h5>Notes for this Plan:</h5>
                <pre>{!! $plan->info !!}</pre>
            @endif
            <br>Add note:<br>
            <textarea name="info"></textarea>
        @endif
    </div>



    @if (isset($plan))

        @if (Auth::user()->isEditor()) &nbsp; 
            <span class="has-warning" onclick="showSpinner()">
            {!! Form::submit('Save changes', [
                'data-toggle' => 'tooltip', 
                'class'       => 'form-submit text-help submit-button plan-details',
                'style'       => 'display: none',
                'title'       => 'Click to save changes to notes, event type, date, leader or teacher',
            ]) !!}
            </span>
            <script>
                // define field that should always get input focus
                document.forms.inputForm.date.focus();
                document.forms.inputForm.date.setAttribute('class', 'main-input');
            </script>

        @else
            &nbsp; {!! Form::submit('Save Note'); !!}
        @endif

        @if ( Auth::user()->isAdmin()  &&  $plan->items->count()==0 ) &nbsp; 
            <a class="btn btn-danger btn-sm" type="button" data-toggle="tooltip"  onclick="showSpinner()"
                title="You can only delete a plan that contains no items." 
                href="{{ url('cspot/plans/delete/'.$plan->id) }}">
                <i class="fa fa-trash" > </i>
                &nbsp; Delete an empty Plan
            </a>
        @endif

    @else
        <script>document.forms.inputForm.leader_id.focus()</script>
    @endif

    
    {!! Form::close() !!}

    <script>
        function enableSaveButton(that) {
            $('.form-submit').removeAttr('disabled');
            blink('.form-submit');
            $(that).parent().addClass('has-warning');
        }
        
        @if (isset($types))
            cSpot.serviceTypes = JSON.parse('{!! addslashes( json_encode($types, JSON_HEX_APOS | JSON_HEX_QUOT) ) !!}');
        @endif

    </script>   



    @if (isset($plan))

        {{-- 
                provide popup to add/insert new item 
        --}}
        @include('cspot.snippets.add_item_modal')

    @endif


    
@stop
