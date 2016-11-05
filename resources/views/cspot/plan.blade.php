
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

<?php Use Carbon\Carbon; ?>

@extends('layouts.main')

@section('title', "Event Planning")

@section('plans', 'active')


@if (session()->has('defaultValues'))
    <?php $defaultValues = session('defaultValues'); ?>
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
        @endif


    @else

        {!! Form::open(array('action' => 'Cspot\PlanController@store', 'id' => 'inputForm')) !!}

    @endif


    
    {{--  page header  
    --}}
    
    <div class="row">
        <div class="col-md-9 col-xl-8 md-center">

            @if ( isset($plan) && $plan->items()->count() )


                {{-- show links to various plan presentation modes 
                --}}
                <div class="dont-print">

                    @if (Auth::user()->isMusician())
                        <div class="float-xs-right">
                            <a title="Show sheetmusic (if available) for the songs on this plan"
                                onclick="$('#show-spinner').modal({keyboard: false});" 
                                href="{{ url('cspot/items/'.$plan->firstItem()->id.'/sheetmusic/') }}">
                                <i class="fa fa-music">&nbsp;</i>Music</a>
                        </div>
                        <div class="float-xs-right mr-1">
                            <a title="Show guitar chords (if available) for the songs on this plan" 
                                onclick="$('#show-spinner').modal({keyboard: false});" 
                                href="{{ url('cspot/items/').'/'.$plan->firstItem()->id }}/chords">
                                &#127928;&nbsp;</i>Chords</a>
                        </div>
                    @endif

                    <div class="float-xs-right mr-1">
                        <a title="Start projector-enabled presentation of each song and scripture reading in this plan" 
                            onclick="$('#show-spinner').modal({keyboard: false});" 
                            href="{{ url('cspot/items/'.$plan->firstItem()->id.'/present/') }}">
                            &#127909;&nbsp;</i>Present</a>
                    </div>

                    @if (Auth::user()->ownsPlan( $plan->id ))
                        <div class="float-xs-right mr-1">
                            <a title="for the Leader: Event script with all items, slides and details" 
                                onclick="$('#show-spinner').modal({keyboard: false});" 
                                href="{{ url('cspot/items/'.$plan->firstItem()->id.'/leader/') }}">
                                &#128483;&nbsp;</i>Lead</a>
                        </div>
                    @endif

                    <div class="float-xs-right mr-1">
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
                <div class="float-xs-left plan-details">
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
                        <a href="{{ url('cspot/plans/'.$plan->id.'/team') }}" class="ml-2 nowrap" 
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
                        <a href="{{ url('cspot/plans/'.$plan->id.'/resource') }}" class="ml-2 nowrap" 
                            onclick="$('#show-spinner').modal({keyboard: false});" title="{{ $resrcList }}">
                            <i class="fa fa-cubes"></i>&nbsp;Resources<small>({{$plan->resources->count()}})</small>
                        </a> 
                </div>
            @endif


            @if ( Auth::user()->isEditor() && isset($plan) && $plan->date >= \Carbon\Carbon::yesterday() ) 
                <div class="float-xs-right small">
                    &nbsp; <a href="#" onclick="$('.plan-details').toggle()">edit plan details</a>
                </div>
                <div class="float-xs-right plan-details small" style="display: none;" title="{{ $plan->updated_at }}">
                    (last changed by {{ $plan->changer }} {{ Carbon::now()->diffForHumans( $plan->updated_at, true ) }} ago)
                </div>
            @endif



            <div class="form-buttons">
                <big>
                    @if (isset($plan))
                        @if (Auth::user()->isEditor())
                            <span class="has-warning mr-1">
                            {!! Form::submit('Save changes', [
                                'data-toggle'    => 'tooltip', 
                                'data-placement' => 'left',
                                'class'          => 'bnt btn-secondary disabled plan-details text-help',
                                'id'             => 'form-submit-btn',
                                'style'          => 'display: none',
                                'title'          => 'Click to save changes to notes, event type, date, leader or teacher',
                                'disabled'       => 'disabled',
                                'aria-disabled'  => "true",
                            ]); !!}</span>
                        @endif

                    @else
                        <input class="xs-width-half" type="submit" value="Submit">
                    @endif
                </big>
            </div>  

        </div>
    </div>


    <div class="plan-details row"{!! isset($plan) ? " style='display: none'" : '' !!}>


        @if (Auth::user()->isEditor())
            <div class="col-xl-4 col-lg-6">
                <div class="card-block narrower bg-muted mb-1">
                    @if (isset($plan))
                        {!! Form::label('type', 'Event Type:', ['class'=>'d-block']); !!}
                    @endif
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
                    
                    <p class="mt-1 mb-0">
                        {!! Form::label('type', 'Subtitle:', ['class'=>'d-block']); !!}
                        {!! Form::text('subtitle') !!}</p>
                    <p class="small text-muted mb-0">(E.g. a location - used in the Announcements slide)</p>

                </div>
            </div>

        
            <div class="col-xl-3 col-lg-6 mb-1">
                <div class="card-block narrower bg-muted">
                    <div class="form-group mb-0">
                        {!! Form::label('date', 'Event Date and Times: ', ['class'=>'d-block']); !!}
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

                        <div class="form-group mt-1 mb-0" id="editPlanServiceTimes">

                            {!! Form::label('start', 'Event runs from:', ['class'=>'d-block']); !!}
                            @if (isset($defaultValues))
                                {!! Form::time( 'start', $defaultValues['start']); !!}
                            @else
                                {!! Form::time( 'start'); !!}
                            @endif

                            {!! Form::label('end', ' to ', ['class'=>'align-baseline']); !!}
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
            </div>                    
        @endif



        <div class="col-xl-5 col-lg-12 mb-1">
            <div class="card-block narrower bg-muted">


                <div class="col-xs-12">
                    <div class="row form-group nowrap mb-0">

                        @if (isset($plan))
                            <span class="float-xs-right small">
                                <a href="{{ url('cspot/history?plan_id=').$plan->id }}">show plan history</a></span>
                        @endif

                        <label class="form-control-label">Leader: &nbsp; &nbsp;
                            <select name="leader_id" id="leader_id" class="text-help c-select" 
                                onchange="enableSaveButton(this); $('.reasonForChange').show(); $('.reasonForChange>input').focus()"
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
                        </label>
                    </div>
                </div>          


                <div class="col-xs-12">
                    <div class="row form-group nowrap mb-0">
                        <label class="form-control-label">Teacher: &nbsp;
                            <select name="teacher_id" class="text-help c-select" 
                                onchange="enableSaveButton(this); $('.reasonForChange').show(); $('.reasonForChange>input').focus()"
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
        
                 @if (isset($plan))
                    <div class="col-xs-12 reasonForChange mb-1" style="display: none;">                
                        <span class="label label-default">Please provide reason for this change:</span>
                        <input type="text" name="reasonForChange" class="fully-width">
                    </div>
                @endif


            </div>                    
        </div>                    

    </div>




    <!-- 
        Show items for existing plan 
        ____________________________
    -->
    @if (isset($plan))



        {{-- list all plan items here 
        --}}
        @include('cspot.items')




        @if (Auth::user()->ownsPlan($plan->id) && $plan->isFuture() )
            <a href="{{ url('cspot/songs?plan_id='.$plan->id) }}"  onclick="showSpinner()"
                title="Search for a song via the full song listing" 
                class="btn btn-sm btn-info float-xs-right">
                    <i class="fa fa-plus"></i><i class="fa fa-music"></i>&nbsp; - Search and add song</a>
        @endif

    @else

        <hr>
        <!-- Checkbox to add default items into NEW plan -->
        <input type="hidden" name="defaultItems" value="false">
        <div class="checkbox">
            <label>
                <input checked="checked" type="checkbox" value="Y" name="defaultItems">
                Insert default items for this plan?
            </label>
        </div>    
        <!-- Checkbox to add default TIMES into NEW plan -->
        <input type="hidden" name="defaultValues" value="false">
        <div class="checkbox">
            <label>
                <input checked="checked" type="checkbox" value="Y" name="defaultValues"
                    onclick="$('#planServiceTimes').toggle()">
                Insert other default values (times, resources) for this plan?
                <br><small class="text-muted">(see: <a href="{{ url('admin/types') }}">List of event types</a>)</small>
            </label>
            <div id="planServiceTimes" style="display: none">
                {!! Form::label('start', 'New times:'); !!}
                {!! Form::time( 'start'); !!}
                {!! Form::label('end', ' - '); !!}
                {!! Form::time( 'end');   !!}      
            </div>
        </div>

        <!-- what to do after creating this plan? Either go to the new plan or add another one of this type -->
        <input type="hidden" name="addAnother" value="false">
        <div class="checkbox">
            <label>
                <input 
                {{ isset($defaultValues) ? 'checked="checked"' : '' }}
                type="checkbox" value="Y" name="addAnother">
                Add another Event Plan after this one?
            </label>
        </div>                

        <hr>

    @endif




    {{-- =================   Plan Notes 
    --}}
    <div style="clear:both; max-width: 50rem;" class="card mt-3">

        <div class="card-block narrower">
            <h5 class="card-title">Notes for this Plan:</h5>

            @if (isset($plan))
                {{-- allow editor to change the whole note for upcoming events --}}
                @if ( Auth::user()->isEditor() && $plan->date >= \Carbon\Carbon::today() )

                    <p title="Click to edit!" class="editable-plan-info card-text narrow white-space-pre-wrap"
                            onclick="location.href='#bottom';" 
                            id="info-plan-id-{{ $plan->id }}">{!! $plan->info !!}</p>
                    @if ( strlen($plan->info) > 0 )
                        <span class="fa fa-eraser text-muted" onclick="erasePlanNote('{{ $plan->id }}')" title="Discard the whole note"></span>
                    @endif
                @else
            
                    <p class="card-text narrower white-space-pre-wrap">{!! $plan->info !!}</p>
                    <p class="card-text narrower white-space-pre-wrap" id="showAddedPlanNote"></p>
                    
                    @include ('cspot.snippets.addnote')

                    <a href="#" data-toggle="modal" data-target="#addPlanNoteModal" class="card-link">Add Note</a>
                    <script>
                        cSpot.plan = {id: {{ $plan->id }} };
                        $('#addPlanNoteModal').css('top','inherit');
                    </script>
                @endif
            @else

                <p class="card-text">{!! Form::textarea('info') !!}</p>
                
            @endif

        </div>

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
            $('#form-submit-btn').removeAttr('disabled');
            $('#form-submit-btn').removeClass('disabled');
            $('#form-submit-btn').removeClass('btn-secondary');
            $('#form-submit-btn').addClass('btn-primary');
            $(that).parent().addClass('has-warning');
        }
        
        @if (isset($types))
            cSpot.serviceTypes = JSON.parse('{!! addslashes( json_encode($types, JSON_HEX_APOS | JSON_HEX_QUOT) ) !!}');
        @endif

    </script>   



    @if (isset($plan))

        {{--  provide popup to add/insert new item 
        --}}
        @include('cspot.snippets.add_item_modal')

    @endif


    <div id="bottom">&nbsp;</div>

@stop
