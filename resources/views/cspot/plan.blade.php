
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

<?php Use Carbon\Carbon; ?>

@extends('layouts.main')

@section('title', "Create or Update a Service Plan")

@section('plans', 'active')


@if (session()->has('defaultValues'))
    <?php $defaultValues = session('defaultValues') ?>
@endif

@section('content')


    @include('layouts.flashing')


    @if (isset($plan))
        @if (Auth::user()->isEditor())
            {!! Form::model( $plan, array(
                'route'  => array('cspot.plans.update', $plan->id), 
                'method' => 'put', 
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
                            href="https://www.youtube.com/playlist?list=PL4XL7HPBoyv9Pcf0ZFWfa2GLY2VKPfZqz">
                            <i class="fa fa-youtube">&nbsp;</i>play all</a>
                    </div>
                </div>
            @endif
            @if ( isset($plan) )

                <h4 class="hidden-md-down">Plan for "{{ $plan->type->name }}" on {{ $plan->date->formatLocalized('%A, %d %B %Y') }}</h4>
                <h4 class="hidden-lg-up">"{{ $plan->type->name }}" on {{ $plan->date->formatLocalized('%a, %d %B') }}</h4>


            @else

                <h3>Add Service Plan</h3>

            @endif

        </div>




        <div class="col-md-3 col-xl-4 right md-center">

            @if (isset($plan))
                <div class="pull-xs-left plan-details">
                    <big>
                        L.:&nbsp;<strong>{{ $plan->leader->name }}</strong> &nbsp;
                        @if ( strtoupper($plan->teacher->name)<>'N/A' )
                            T.:&nbsp;<strong>{{ $plan->teacher->name }}</strong>
                        @endif
                        <?php
                            $teamList = ''; // create the list of team members and their roles for this plan
                            foreach ( $plan->teams as $key => $team ) {
                                $teamList .= $team->user->name . ' as ';
                                $teamList .= $team->role ? ucfirst($team->role->name) : '(tbd)';
                                $teamList .= $team->confirmed ? ' (confirmed)' : ' (unconfirmed)';
                                if ($key+1 < $plan->teams->count())
                                    $teamList .= ",\n";
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
                                $resrcList .= ' ('+$resrc->pivot->comment+')'; 
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
                <div class="pull-xs-right plan-details small">
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
                                'class'          => 'form-submit text-help',
                                'style'          => 'display: none',
                                'disabled'       => 'disabled',
                                'title'          => 'Click to save changes to notes, service type, date, leader or teacher',
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
                <div class="row form-group">
                    <select name="type_id" class="form-control text-help plan-form-minw c-select" 
                        onchange="fillDefaultServiceTimes(this)">
                        @if (! isset($plan) && ! isset($defaultValues['type_id'] ))
                            <option selected>
                                Select ...
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
                        {!! Form::label('start', 'Service Time:'); !!}
                        {!! Form::time( 'start'); !!}
                        {!! Form::label('end', ' - '); !!}
                        {!! Form::time( 'end');   !!}      
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
                <div class="row form-group nowrap">
                    <label class="form-control-label">Leader </label>
                    <select name="leader_id" class="form-control text-help c-select" onchange="enableSaveButton(this)"
                            {{ Auth::user()->isEditor() ? '' : ' disabled' }}>
                        @if (! isset($plan))
                            <option selected>
                                Select ...
                            </option>
                        @endif
                        @foreach ($users as $user)
                            @if( $user->hasRole('leader'))
                                <option 
                                    @if(   ( ''<>old('leader_id') && $user->id==old('leader_id') )  
                                        || ( isset($plan) && $plan->leader_id==$user->id ) )
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
                            <option selected>
                                Select ...
                            </option>
                            <option value="0">None</option>
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
        <input type="hidden" name="defaultTimes" value="false">
        <div class="checkbox center">
            <label>
                <input checked="checked" type="checkbox" value="Y" name="defaultTimes"
                    onclick="$('#planServiceTimes').toggle()">
                Insert default Start- and End-times for this plan?
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
                Add another service plan after this one?
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
                'class'       => 'form-submit text-help submit-button',
                'style'       => 'display: none',
                'disabled'    => 'disabled',
                'title'       => 'Click to save changes to notes, service type, date, leader or teacher',
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

        @if (isset($plan))
            var haystackMP = JSON.parse('{!! json_encode($mp_song_list, JSON_HEX_APOS | JSON_HEX_QUOT) !!}');
            // example: {"id":10,"title":"A Safe Stronghold Our God Is Still","book_ref":"MP2","title_2":"","number":"2"}
            function showHint(needle) {
                if (needle.length == 0) {
                    $('#txtHint').html('');
                    return;
                }
                var count=0;
                var found = 'no match';
                needle = needle.toLowerCase();
                for (var i=0; i<haystackMP.length; i++) {
                    if ( haystackMP[i].title.toLowerCase().indexOf(needle)>=0 
                      || haystackMP[i].title_2.toLowerCase().indexOf(needle)>=0 
                      || haystackMP[i].book_ref.toLowerCase().indexOf(needle)>=0 ) {
                        if (count==0) found='';
                        found+='<div class="radio"><label><input type="radio" onclick="$(\'#searchForSongsButton\').click();" name="haystack" id="needle-';
                        found+=haystackMP[i].id + '" value="'+ haystackMP[i].id;
                        found+='">' + haystackMP[i].book_ref + ' ' + haystackMP[i].title + '</label></div>';
                        count++;
                    }
                    if (count>5) break;
                };
                $('#txtHint').html(found);
            }
        @endif
        
        @if (isset($types))
            var serviceTypes = JSON.parse('{!! json_encode($types, JSON_HEX_APOS | JSON_HEX_QUOT) !!}');
        @endif

    </script>   



@if (isset($plan))
    <!-- Modal to search for new song -->
    <form id="searchSongForm" action="{{url('cspot/items')}}" method="POST">
        <div class="modal fade" id="searchSongModal" tabindex="-1" role="dialog" aria-labelledby="searchSongModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">
                            <span id="searchSongModalLabel">Select what to insert</span> <span id="modal-show-item-id"></span>
                        </h4>

                        <a href="#" class="btn btn-lg btn-outline-primary modal-pre-selection fully-width"
                            onclick="showModalSelectionItems('song')"       >Song       </a>
                        <a href="#" class="btn btn-lg btn-outline-success modal-pre-selection fully-width"
                            onclick="showModalSelectionItems('scripture')"  >Scripture  </a>
                        <a href="#" class="btn btn-lg btn-outline-info modal-pre-selection fully-width"
                            onclick="showModalSelectionItems('comment')"    >Comment    </a>
                    </div>

                    <div class="modal-body modal-select-comment modal-select-song modal-select-scripture" style="display: none;">
                        <input type="text"   id="comment" name="comment"
                            class="center-block m-b-1 modal-select-comment modal-input-comment modal-select-scripture fully-width">

                        <span class="modal-select-scripture">
                            @include( 'cspot.snippets.scripture_input', ['part' => 'one'] )
                            <br>
                            @include( 'cspot.snippets.scripture_input', ['part' => 'two'] )
                        </span>

                        <label id="search-action-label" class="center-block m-b-1 modal-select-song">Full-text search incl. lyrics:</label>
                        <input type="text"   id="search-string" class="search-input search-form-item center-block m-b-1 modal-select-song modal-input-song">

                        <label class="search-form-item modal-select-song" for="MPselect">...or select Mission Praise number</label>
                        <select class="form-control m-b-1 search-form-item modal-select-song" id="MPselect" onchange="$('#searchForSongsButton').click();">
                            <option value="0">select....</option>
                            {{-- only add MP songs --}}
                            @foreach ($mp_song_list as $song){!!substr($song->book_ref,0,2)=='MP' ? '<option value="'.$song->id.'">'.$song->number.' - '.$song->title.'</option>' : ''!!}@endforeach
                        </select>

                        <label for="haystack" class="search-form-item modal-select-song">..or search Song title or number</label>
                        <input type="text" class="form-control search-form-item modal-select-song" id="haystack" onkeyup="showHint(this.value)">
                        <div class="search-form-item modal-select-song" id="txtHint"></div>

                        <input type="hidden" id="seq-no">
                        <input type="hidden" id="plan_id"       name="plan_id" data-search-url="{{ url('cspot/songs/search') }}">
                        <input type="hidden" id="beforeItem_id" name="beforeItem_id">
                        <input type="hidden" id="song_id"       name="song_id">
                        {{ csrf_field() }}

                        <div id="search-result"></div>
                        <div id="searching" style="display: none;">
                            <i class="fa fa-spinner fa-pulse fa-lg fa-fw"></i>&nbsp;<span>leafing through the pages ...</span>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary modal-select-song modal-select-comment modal-select-scripture" 
                            type="button" onclick="resetSearchForSongs()">Restart</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="resetSearchForSongs()">Cancel</button>
                        <a href="#" class="btn btn-primary modal-select-song" id="searchForSongsButton" onclick="searchForSongs()">Search</a>
                        <button type="submit" class="btn btn-primary" 
                            id="searchForSongsSubmit" onclick="searchForSongs(this)">Submit</button>
                    </div>

                </div>
            </div>
        </div>
    </form>
@endif
    
@stop
