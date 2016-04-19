
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Create or Update a Service Plan")

@if (isset($plan))
    @section('plans', 'active')
@else
    @section('create', 'active')
@endif


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
                        href="{{ url('cspot/items/'.$plan->firstItem()->id.'/sheetmusic/') }}">
                        <i class="fa fa-file-text">&nbsp;</i>Sheetmusic</a>
                </div>
                <div class="pull-xs-right m-r-1">
                    <a title="Show guitar chords (if available) for the songs on this plan" 
                        href="{{ url('cspot/items/').'/'.$plan->firstItem()->id }}">
                        <i class="fa fa-music">&nbsp;</i>Chords</a>
                </div>
                <div class="pull-xs-right m-r-1">
                    <a title="Start projector-enabled presentation of each song and scripture reading in this plan" 
                        href="{{ url('cspot/items/'.$plan->firstItem()->id.'/present/') }}">
                        <i class="fa fa-tv">&nbsp;</i>Present</a>
                </div>
                <div class="pull-xs-right m-r-1">
                    <a title="YouTube playlist of all songs" target="new" 
                        href="https://www.youtube.com/playlist?list=PL4XL7HPBoyv9Pcf0ZFWfa2GLY2VKPfZqz">
                        <i class="fa fa-youtube">&nbsp;</i>play all</a>
                </div>
            </div>

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
                        Leader:&nbsp;<strong>{{ $plan->leader->first_name }}</strong> &nbsp;
                        @if ( strtoupper($plan->teacher->first_name)<>'NONE' )
                            Teacher:&nbsp;<strong>{{ $plan->teacher->first_name }}</strong>
                        @endif
                    </big>
                </div>
            @endif


            @if ( Auth::user()->isEditor() && isset($plan) ) 
                <div class="pull-xs-right plan-details">
                    &nbsp; (<a href="#" onclick="$('.plan-details').toggle()">Edit Plan Details</a>)
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


    <div class="plan-details row center" 
        @if (isset($plan))
            style="display: none"
        @endif
    >


        @if (Auth::user()->isEditor())
            <div class="col-xl-4 col-lg-6">
                <div class="row form-group">
                    <!-- <label class="form-control-label plan-form-minw right hidden-sm-down">Type of Service</label>                  -->
                    <select name="type_id" class="form-control text-help plan-form-minw c-select" onchange="enableSaveButton(this)">
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
                                    value="{{ $user->id }}">{{ $user->first_name }}
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
                    @if ( ! isset($plan) || (isset($plan) && Auth::user()->ownsPlan($plan->id)) )
                        <big>
                            <a tabindex="0" href="#"
                                data-container="body" data-toggle="tooltip"
                                title="Select 'none' if the leader is also the teacher">
                                <i class="fa fa-question-circle"></i></a>
                        </big>
                    @endif
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
                                    value="{{ $user->id }}">{{ $user->first_name }}</option>
                            @endif
                        @endforeach
                    </select>
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

    @else

        <!-- Checkbox to add default items into NEW plan -->
        <input type="hidden" name="defaultItems" value="false">
        <div class="checkbox center">
            <label>
                <input checked="checked" type="checkbox" value="Y" name="defaultItems">
                Insert default items for this plan?
            </label>
        </div>                

        <!-- what to do after creating this plan? Either go to this plan or add another one of this type -->
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





    <div class="form-group
        @if (! isset($plan))
            center
        @endif
        ">
        @if (Auth::user()->isEditor())
            {!! Form::label('info', 'Notes:', ['class' => 'form-control-label']); !!}
            <br/>
            {!! Form::textarea('info') !!}
            <script>
                document.forms.inputForm.info.rows=5;
                $('#info').attr('onchange',"enableSaveButton(this)");
            </script>
        @else
            @if ($plan->info)
                <h5>Notes for this Plan:</h5>
                <pre>{!! $plan->info !!}</pre>
            @endif
            <br>Add note:<br>
            <textarea name="info"></textarea>
        @endif
    </div>



    @if (isset($plan))

        @if (Auth::user()->isEditor()) &nbsp; 
            <span class="has-warning">
            {!! Form::submit('Save changes', [
                'data-toggle' => 'tooltip', 
                'class'       => 'form-submit text-help',
                'style'       => 'display: none',
                'disabled'    => 'disabled',
                'title'       => 'Click to save changes to notes, service type, date, leader or teacher',
            ]) !!}
            </span>
            <script>
                // define field that should always get input focus
                document.forms.inputForm.date.focus();
                document.forms.inputForm.date.setAttribute('class', 'main-input');

                function enableSaveButton(that) {
                    $('.form-submit').removeAttr('disabled');
                    blink('.form-submit');
                    $(that).parent().addClass('has-warning');
                }
            </script>

        @else
            &nbsp; {!! Form::submit('Save Note'); !!}
            <script>document.forms.inputForm.info.focus()</script>
        @endif

        @if ( Auth::user()->isAdmin()  &&  $plan->items->count()==0 ) &nbsp; 
            <a class="btn btn-danger btn-sm" type="button" data-toggle="tooltip" 
                title="You can only delete a plan that contains no items." 
                href="{{ url('cspot/plans/'.$plan->id) }}/delete">
                <i class="fa fa-trash" > </i>
                &nbsp; Delete an empty Plan
            </a>
        @endif

    @else
        <script>document.forms.inputForm.leader_id.focus()</script>
    @endif

    
    {!! Form::close() !!}

    
@stop