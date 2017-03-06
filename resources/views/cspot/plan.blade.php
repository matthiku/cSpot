
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

    @include('cspot/snippets/modal')



    {{-- html input form definition 
    --}}
    @if (isset($plan))


        {{--  provide popup to add/insert new item 
        --}}
        @include('cspot.snippets.add_item_modal')



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
    
    <div class="row mx-auto">
        <div class="col-md-9 col-xl-8">

            @if ( isset($plan) && $plan->items()->count() )


                {{-- show links to various plan presentation modes 
                --}}
                <div class="dont-print plan-details">

                    @if (Auth::user()->isMusician())
                        <div class="float-right ml-2">
                            <a title="Show sheetmusic (if available) for the songs on this plan"
                                onclick="$('#show-spinner').modal({keyboard: false});" 
                                href="{{ url('cspot/items/'.$plan->firstItem()->id.'/sheetmusic/') }}">
                                <i class="fa fa-music">&nbsp;</i>Music</a>
                        </div>
                        <div class="float-right mx-1">
                            <a title="Show guitar chords (if available) for the songs on this plan" 
                                onclick="$('#show-spinner').modal({keyboard: false});" 
                                href="{{ url('cspot/items/').'/'.$plan->firstItem()->id }}/chords">
                                &#127928;Chords</a>
                        </div>
                    @endif

                    <div class="float-right mx-1">
                        <a title="Start projector-enabled presentation of each song and scripture reading in this plan" 
                            onclick="$('#show-spinner').modal({keyboard: false});" 
                            href="{{ url('cspot/items/'.$plan->firstItem()->id.'/present/') }}">
                            <i class="fa fa-tv fa-lg"></i>Present</a>
                    </div>

                    @if (Auth::user()->ownsPlan( $plan->id ))
                        <div class="float-right mr-2">
                            <a title="for the Leader: Event script with all items, slides and details" 
                                onclick="$('#show-spinner').modal({keyboard: false});" 
                                href="{{ url('cspot/items/'.$plan->firstItem()->id.'/leader/') }}">
                                &#128483;Lead</a>
                        </div>
                    @endif

                    {{-- currently not used
                    <div class="float-right mr-1">
                        <a title="YouTube playlist of all songs" target="new" 
                            href="{{ env('YOUTUBE_PLAYLIST_URL', 'https://www.youtube.com/playlist?list=').env('CHURCH_YOUTUBE_PLAYLIST_ID', '') }}">
                            <i class="fa fa-youtube">&nbsp;</i>play all</a>
                    </div>
                    --}}  

                </div>
            @endif

            @if ( isset($plan) )

                {{-- Plan Title 
                --}}
                <h4 class="hidden-md-down">
                    <span class="text-success lora font-weight-bold">{{ $plan->type->generic ? $plan->subtitle : $plan->type->name }}</span>
                    <span class="small font-weight-bold">on {{ $plan->date->formatLocalized('%A, %d %B %Y') }}</span>
                    @include ('cspot.snippets.details_link')
                </h4>
                <h4 class="hidden-lg-up float-left font-weight-bold">
                    <span class="text-success lora">{{ $plan->type->generic ? $plan->subtitle : $plan->type->name }}</span>
                    on <span class="text-danger">{{ $plan->date->formatLocalized('%a, %d %b') }}</span>
                    @include ('cspot.snippets.details_link')
                </h4>
                <small class="hidden-lg-down plan-details">{{ $plan->type->generic ? '' : $plan->subtitle ? '('.$plan->subtitle.')' : ''  }}</small>


            @else

                <h3>Add Event Plan</h3>

            @endif

        </div>




        <div class="col-md-3 col-xl-4 right">

            @if (isset($plan))
                {{-- Show team and resources 
                --}}
                <div class="plan-details">
                    <span>
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
                            data-template='<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><pre class="tooltip-inner tooltip-medium"></pre></div>'
                            data-placement="bottom" data-toggle="tooltip" title="{{ $teamList }}">
                            <i class="fa fa-users"></i>&nbsp;Team<small>({{$plan->teams->count()}})</small>
                        </a> 
                        L.:&nbsp;<strong>{{ $plan->leader ? $plan->leader->name : $plan->leader_id }}</strong> &nbsp;
                        @if ( strtoupper($plan->teacher->name)<>'N/A' )
                            T.:&nbsp;<strong>{{ $plan->teacher->name }}</strong>
                        @endif
                    </span>
                    <small>
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
                            data-placement="left" data-toggle="tooltip" title="{{ $resrcList }}">
                            <i class="fa fa-cubes"></i>&nbsp;Resources<small>({{$plan->resources->count()}})</small>
                        </a> 
                    </small>
                </div>
            
                <div class="d-inline plan-details small" style="display: none;" title="{{ $plan->updated_at }}">
                    (last changed by {{ $plan->changer }} {{ Carbon::now()->diffForHumans( $plan->updated_at, true ) }} ago)
                </div>
            @endif


            {{-- Submit or Save button 
            --}}
            <div class="form-buttons float-left">
                <big>
                    @if (isset($plan))
                        @if (Auth::user()->isEditor())
                            <span class="has-warning mr-1">
                            {!! Form::submit('Save changes', [
                                'data-toggle'    => 'tooltip', 
                                'data-placement' => 'left',
                                'class'          => 'btn btn-outline-success submit-button disabled plan-details text-help',
                                'id'             => 'form-submit-btn',
                                'style'          => 'display: none',
                                'title'          => 'Click to save changes to notes, event type, date, leader or teacher',
                                'disabled'       => 'disabled',
                                'aria-disabled'  => "true",
                            ]); !!}</span>
                        @endif

                    @else
                        <input class="btn btn-outline-success btn-block submit-button{{ isset($defaultValues['type_id']) ? '' : ' disabled'}}" type="submit" value="Submit">
                    @endif
                </big>
            </div>  



        </div>
    </div>


    {{-- PLAN DETAILS 
    --}}
    <div class="plan-details row mx-1"{!! isset($plan) ? " style='display: none'" : '' !!}>



        @if (Auth::user()->isEditor())

            <div class="col-xl-4 col-lg-6">
                <div class="card-block narrower bg-muted mb-1">

                    @if (isset($plan))
                        <div class="float-right">
                            {!! Form::label('private', 'Make Private?', ['class'=>'d-block']); !!}
                            <label class="custom-control custom-checkbox float-right">
                                <input name="private" type="checkbox" onclick="togglePlanPrivate( this, {{ $plan->id }} )"
                                      class="custom-control-input"{{ $plan->private ? ' checked="checked"' : '' }}>
                                <span class="custom-control-indicator"></span>
                                <span class="small custom-control-description plan-private-field">(Hide on Announcements)</span>
                            </label>
                        </div>

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
                    
                    <p class="mt-1 mb-0" id="edit-subtitle">
                        <label for="type" class="d-block">Subtitle <span class="text-muted">(or: Title for generic events)</span>:</label>
                        
                        @if ( isset($plan) )
                            {!! Form::text('subtitle', $plan->subtitle, ['onfocus' => 'enableSaveButton(this)']) !!}</p>
                        @elseif ( isset($defaultValues['type_id']) )
                            {!! Form::text('subtitle', $defaultValues['subtitle'], ['onfocus' => 'enableSaveButton(this)']) !!}</p>
                        @else
                            {!! Form::text('subtitle') !!}</p>
                        @endif
                    <p class="small text-muted mb-0">(E.g. a location - used in the Announcements slide)</p>

                </div>
            </div>

        
            <div class="col-xl-3 col-lg-6 mb-1">
                <div class="card-block narrower bg-muted">
                    <div class="form-group mb-0 text-center" id="editPlanDate">
                        {!! Form::label('date', 'Event Date and Times: ', ['class'=>'d-block']); !!}
                        @if ( isset($plan) )
                            {!! Form::text( 
                                'date', $plan->date, 
                                ['class'    => 'plan-form-minw center' ] ) 
                            !!}
                        @elseif (isset($defaultValues['date']))
                            {!! Form::text( 
                                'date', $defaultValues['date'], 
                                ['class' => 'plan-form-minw center' ] )
                            !!}
                        @else
                            {!! Form::text( 
                                'date', \Carbon\Carbon::now(), 
                                ['class' => 'plan-form-minw center' ] )
                            !!}
                        @endif

                        <div class="form-group mt-1 mb-0" id="editPlanServiceTimes">

                            {!! Form::label('start', 'Begin and End:', ['class'=>'d-block']); !!}
                            @if (isset($defaultValues['type_id']))
                                {!! Form::text( 'start', $defaultValues['start']); !!}
                            @else
                                {!! Form::text( 'start'); !!}
                            @endif

                            {!! Form::label('end', ' to ', ['class'=>'align-baseline']); !!}
                            @if (isset($defaultValues['type_id']))
                                {!! Form::text( 'end', $defaultValues['end']); !!}
                            @else
                                {!! Form::text( 'end' ); !!}
                            @endif

                        </div>

                        <script>
                            // assign IDs to the date+time input fields
                            $($('#editPlanDate').children('input')[0]).attr('id', 'plan-date');
                            $($('#editPlanServiceTimes').children('input')[0]).attr('id', 'plan-date-start-time');
                            $($('#editPlanServiceTimes').children('input')[1]).attr('id', 'plan-date-end-time');

                            // set width of time input fields
                            $('#plan-date').css('width', '10rem');
                            $('#plan-date-start-time').css('width', '4rem');
                            $('#plan-date-end-time'  ).css('width', '4rem');

                            // limit plan date to date (without time)
                            $('#plan-date').val($('#plan-date').val().substr(0,10));
                            // limit times to hour and minute
                            if ($('#plan-date-end-time').val().length>5)
                                $('#plan-date-end-time').val($('#plan-date-end-time').val().substr(0,5));
                            if ($('#plan-date-start-time').val().length>5)
                                $('#plan-date-start-time').val($('#plan-date-start-time').val().substr(0,5));

                            $('#plan-date').datepicker({
                                dateFormat: "yy-mm-dd",
                                showAnim: "slideDown",
                                onSelect: 'enableSaveButton(this)',
                            });

                            // activate the timepicker for Plan start- and end-time
                            $('#plan-date-start-time').timepicker({
                                showSecond: false,
                                stepMinute: 5,
                                timeOnlyTitle: 'Select Start Time',
                                closeText: 'Set',
                                onSelect: 'enableSaveButton(this)',
                            });
                            $('#plan-date-end-time').timepicker({
                                showSecond: false,
                                stepMinute: 5,
                                timeOnlyTitle: 'Select End Time',
                                closeText: 'Set',
                                onSelect: 'enableSaveButton(this)',
                            });
                        </script>
                    </div>
                </div>                    
            </div>                    
        @endif



        <div class="col-xl-5 col-lg-12 mb-1">
            <div class="card-block narrower bg-muted">


                <div class="col-xs-12">
                    <div class="form-group nowrap mb-0">

                        @if (isset($plan))
                            <span class="float-right small">
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
                                <a href="{{ url('cspot/plans/'.$plan->id.'/remind/'.$plan->leader_id) }}?role=leader" 
                                   class="btn btn-sm btn-secondary" role="button"
                                   data-toggle="tooltip" title="Send reminder to leader to insert missing items">
                                    <i class="fa fa-envelope"></i></a>
                            @endif
                        </label>
                    </div>
                </div>          


                <div class="col-xs-12">
                    <div class="form-group nowrap mb-0">
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
                            @if ( ! isset($plan) )
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
                                <a href="{{ url('cspot/plans/'.$plan->id.'/remind/'.$plan->teacher_id) }}?role=teacher" 
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







    @if (isset($plan))
        {{-- 
            Show items for existing plan 
            ____________________________
        --}}



        {{-- 
            List all plan items here 
        --}}
        @include('cspot.items')





        @if ($plan->songsFreshness()>0 && Auth::user()->ownsPlan( $plan->id ))
            <small class="hidden-sm-down mx-4"><span class="hidden-md-down">Songs </span>overall: 
                    <big>{{ $plan->songsFreshness()>50 ? '&#127823;' : '&#127822;' }}</big> {{ number_format( $plan->songsFreshness(), 0 ) }}% 'freshness'
                <a href="#" title="What's that?" onclick="showSongFreshnessHelp()">
                <i class="fa fa-question-circle fa-lg text-danger"></i></a>
            </small>
        @endif

        @if (Auth::user()->ownsPlan($plan->id))
            <a href="#" title="" class="ml-4 hidden-sm-down bg-warning rounded px-1" 
                onclick="showYTvideoInModal('{{ App\Models\Song::where('book_ref', 'tr02')->value('youtube_id') }}', this)" 
                data-toggle="tooltip" data-song-title="Learn how to use this page" data-original-title="Watch this short video to quickly learn how to work on this page."
                ><span class="nowrap"
                ><i class="fa fa-youtube-play red"></i> Learn <span class="hidden-lg-down">to use this page</span>(P<span class="hidden-lg-down">art</span> 1)</span></a>
            <a href="#" title="" class="ml-1 hidden-sm-down bg-warning rounded px-1" 
                onclick="showYTvideoInModal('{{ App\Models\Song::where('book_ref', 'tr02b')->value('youtube_id') }}', this)" 
                data-toggle="tooltip" data-song-title="Learn how to use this page" data-original-title="Watch this short video to quickly learn how to work on this page.">
                <i class="fa fa-youtube-play red"></i> Part 2</a>
        @endif

        @if (Auth::user()->ownsPlan($plan->id) && $plan->isFuture() )
            <a href="{{ url('cspot/songs?plan_id='.$plan->id) }}"  onclick="showSpinner()"
                title="Search for a song via the full song listing" 
                class="btn btn-sm btn-info float-right">
                    <i class="fa fa-plus"></i><i class="fa fa-music"></i>&nbsp; <span class="hidden-md-down">- Search and </span>add song</a>
        @endif

    @else

        <hr>
        <!-- Checkbox to add default items into NEW plan -->
        <input type="hidden" name="defaultItems" value="false">
        <div class="checkbox">
            <label>
                <input checked="checked" type="checkbox" value="Y" name="defaultItems">
                Insert default items for this plan? <span class="text-muted">(See list of <a target="new" href="{{ url('admin/default_items') }}">Default Items</a>)</span>
            </label>
        </div>    

        <!-- what to do after creating this plan? Either go to the new plan or add another one of this type -->
        <input type="hidden" name="addAnother" value="false">
        <div class="checkbox">
            <label>
                <input 
                {{ isset($defaultValues['type_id']) ? 'checked="checked"' : '' }}
                type="checkbox" value="Y" name="addAnother">
                Keep adding new Event Plans of the same type after this one?
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

        @if (Auth::user()->isEditor())
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
                <i class="fa fa-trash" > </i> &nbsp; Delete an empty Plan
            </a>
        @endif

    @else
        @if (isset($defaultValues['type_id']))
            <script>document.forms.inputForm.leader_id.focus()</script>
        @else
            <script>document.forms.inputForm.type_id.focus()</script>
        @endif
    @endif

    
    {!! Form::close() !!}

    <script>
        
        @if (isset($types))
            cSpot.serviceTypes = JSON.parse('{!! addslashes( json_encode($types, JSON_HEX_APOS | JSON_HEX_QUOT) ) !!}');
        @endif

        function showSongFreshnessHelp() {
            var modalContent = "<p>In the first place, songs should be selected as appropriate for the occasion, not by statistical considerations.</p> \
                <p>The 'Song Freshness Index' is only provided in order to help better to understand how often the songs \
                    in this plan have been used before (by all leaders and by you) and when it was the last time they were used in a service.</p> \
                <p>Each song added to this plan receives its own index and the numbers used for the calculation can be looked up by pointing to the individual index.</p> \
                <p>An average 'freshness' index of all songs of this plan is shown at the bottom.</p> "

            // write the modal title
            $('#snippet-modal-title').text("Songs Freshness? What's that?");

            // replace the modal content with the help text
            $('#snippet-modal-content')
                .html(modalContent);

            // open the modal
            $('.help-modal').modal();            
        }

    </script>   


    <div id="bottom">&nbsp;</div>

@stop
