
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Create or Update a User Type")

@section('types', 'active')



@section('content')


    @include('layouts.flashing')


    <div class="row">
        <div class="col-xl-6 offset-xl-3 col-md-8 offset-md-2 col-sm-10 offset-sm-1">                
            @if (isset($type))
                {!! Form::model( $type, array('route' => array('types.update', $type->id), 'method' => 'put', 'id' => 'inputForm', 'oninput' => 'enableSubmitButton()') ) !!}
            @else
                {!! Form::open(array('action' => 'Admin\TypeController@store', 'id' => 'inputForm', 'oninput' => 'enableSubmitButton()')) !!}
            @endif


            <div class="row mb-2">

                <div class="col-sm-6 bg-info">
                    <div class="lora float-sm-right">
                        @if (isset($type))
                            <h3>Update Type</h3>
                        @else
                            <h3>Create Type</h3>
                        @endif
                    </div>
                </div>

                <div class="col-sm-4 ">
                    <span><i class="red">*</i> = mandatory field(s)</span>
               </div>

            </div>

            <hr>


            <div class="row mb-1">
                <div class="col-sm-6">
                    <div class="float-sm-right">

                    {!! Form::label('name', 'Type Name'); !!} <i class="red">*</i>

                    </div>
                </div>
                <div class="col-sm-6">

                   {!! Form::text('name'); !!}
                    @if ($errors->has('name'))
                        <br><span class="help-block">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif

               </div>
            </div>


            <div class="row mb-1">
                <div class="col-sm-6">
                    <div class="text-sm-right">
                        {!! Form::label('generic', 'Is this a generic Type?'); !!}
                    </div>
                </div>

                <div class="col-sm-6">    
                    <div class="btn btn-secondary float-xs-left mr-1" onclick="toggleBooleanButton(this);">
                        {!! Form::hidden('generic', '0') !!}
                        {!! Form::hidden('generic') !!}
                        <span>{!! isset($type) ? $type->generic ? '&#10004;' : '&#10008;' : '&#10008;' !!}</span>
                    </div>    
                    <div class="small narrow"><strong>Note:</strong> A generic event type has no default values and can be used for all kinds of events. 
                    When creating a new event of that type, the new subtitle will be used as the main title (or name) of the actual event.<br>
                    <span class="text-danger">There should be only one generic event type</span> - see list below.</div>
               </div>
            </div>



            <div class="row other-stuff text-xs-center" style="display: none;">
                <a href="#" class="small" onclick="$('.other-stuff').toggle();">show other fields</a>
            </div>

            <div class="row other-stuff">
                <div class="col-sm-6">
                    <div class="float-sm-right">

                    {!! Form::label('subtitle', 'Subtitle or Info or Location'); !!}

                    </div>
                </div>
                <div class="col-sm-6">

                   {!! Form::text('subtitle'); !!}

               </div>
            </div>



            <div class="row mb-1 other-stuff">
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        
                        <label>Usual event times</label>

                    </div>
                </div>
                <div class="col-sm-6">
                    
                    <div class="row">
                        <div class="col-xs-4 col-md-5 col-lg-3 col-xl-2">                        
                            {!! Form::label('start', 'Start'); !!}<br>
                            {!! Form::time('start'); !!}
                        </div>
                        <div class="col-xs-6">
                            {!! Form::label('end', 'End'); !!}<br>
                            {!! Form::time('end'); !!}
                        </div>
                    </div>

               </div>
            </div>



            <div class="row mb-1 other-stuff">
                <div class="col-sm-6">
                    <div class="float-sm-right">

                        {!! Form::label('repeat', 'Interval (repeat)'); !!}

                    </div>
                </div>
                <div class="col-sm-6">

                    <select name="repeat" class="form-control text-help c-select"
                            {{ Auth::user()->isEditor() ? '' : ' disabled' }}>
                        <option {{ (isset($type) && !$type->repeat) ? '' : 'selected'}} value="null">
                            Select ...
                        </option>
                        @foreach (array('daily','weekly','biweekly','fortnightly','monthly','quarterly','half-yearly','yearly') as $val)
                            <option 
                                @if(   ( ''<>old('repeat') && $val==old('repeat') )  
                                    || ( isset($type) && $type->repeat===$val ) )
                                        selected
                                @endif
                                value="{{ $val }}">{{ $val }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('repeat'))
                        <span class="help-block">
                            <strong>{{ $errors->first('repeat') }}</strong>
                        </span>
                    @endif

               </div>
            </div>



            <div class="row mb-1 other-stuff">
                <div class="col-sm-6">
                    <div class="float-sm-right">

                    {!! Form::label('weekday', 'Weekday'); !!}

                    </div>
                </div>
                <div class="col-sm-6">

                    <select name="weekday" class="form-control text-help c-select"
                            {{ Auth::user()->isEditor() ? '' : ' disabled' }}>
                        <option {{ (isset($type) && !$type->weekday) ? '' : 'selected'}} value="null">
                            Select ...
                        </option>
                        @foreach (array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') as $key => $day)
                            <option 
                                @if(   ( ''<>old('weekday') && $key==old('weekday') )  
                                    || ( isset($type) && $type->weekday===$key ) )
                                        selected
                                @endif
                                value="{{ $key }}">{{ $day }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('weekday'))
                        <span class="help-block">
                            <strong>{{ $errors->first('weekday') }}</strong>
                        </span>
                    @endif

               </div>
            </div>



            <div class="row mb-1 other-stuff">
                <div class="col-sm-6">
                    <div class="float-sm-right">

                        {!! Form::label('leader_id', 'Default Leader'); !!}

                    </div>
                </div>
                <div class="col-sm-6">

                    <select name="leader_id" class="form-control text-help c-select"
                            {{ Auth::user()->isEditor() ? '' : ' disabled' }}>
                        <option selected value="null">
                            Select ...
                        </option>
                        @foreach ($users as $user)
                            @if( $user->hasRole('leader'))
                                <option 
                                    @if(   ( ''<>old('leader_id') && $user->id==old('leader_id') )  
                                        || ( isset($type) && $type->leader_id==$user->id ) )
                                            selected
                                    @endif
                                    value="{{ $user->id }}">{{ $user->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>

               </div>
            </div>



            <div class="row mb-1 other-stuff">
                <div class="col-sm-6">
                    <div class="float-sm-right">

                        {!! Form::label('resource_id', 'Default Resource'); !!}

                    </div>
                </div>
                <div class="col-sm-6">

                    <select name="resource_id" class="form-control text-help c-select"
                            {{ Auth::user()->isEditor() ? '' : ' disabled' }}>
                        <option selected value="null">
                            Select ...
                        </option>
                        @foreach ($resources as $resource)
                            <option 
                                @if(   ( ''<>old('resource_id') && $resource->id==old('resource_id') )  
                                    || ( isset($type) && $type->resource_id==$resource->id ) )
                                        selected
                                @endif
                                value="{{ $resource->id }}">{{ $resource->name }}
                            </option>
                        @endforeach
                    </select>

               </div>
            </div>



            <hr>

            <div class="row">
                <div class="col-sm-6">

                    @if (isset($type))
                        <a class="btn btn-sm btn-outline-primary float-xs-left" 
                                href="{{ url('cspot/plans/create') }}?type_id={{ $type->id }}"
                                title="Create a new Event of this type">
                            <i class="fa fa-plus"> </i> Create new Event
                        </a>
                    @endif

                    <div class="float-sm-right">

                        @if (isset($type))
                            <button type="submit" class="btn btn-sm btn-outline-success submit-button disabled">&#10003; Update</button>
                        @else
                            <button type="submit" class="btn btn-sm btn-outline-success submit-button disabled">&#10004; Submit</button>
                        @endif

                    </div>
                </div>
                <div class="col-sm-6">

                    @if (isset($type) && $type->plans->count() == 0)
                        <a class="btn btn-sm btn-danger float-xs-right" href="{{ url('admin/types/'.$type->id) }}/delete">
                            <i class="fa fa-trash" > </i> &nbsp; Delete
                        </a>
                    @endif

                    <a href="{{url('admin/types')}}">{!! Form::button('&#10008; Cancel', ['class'=>'btn btn-sm btn-secondary link cancel-button']); !!}</a></p>

               </div>
            </div>
            
            {!! Form::close() !!}

    
            <hr>

            <h6>List of existing Event Types:</h6>
            <table class="table table-striped table-sm">
                <thead class="thead-default">
                    <tr>
                        <th>Name</th>
                        <th>Generic?</th>
                        <th>Interval</th>
                        <th>Weekday</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($types as $typ)
                        <tr class="{{ (isset($type) && $typ->id == $type->id) ? 'text-info' : '' }}">
                            <td>{{ $typ->name }}{{ $typ->subtitle ? ' ('.$typ->subtitle.')' : '' }}</th>
                            <td>{{ $typ->generic ? '&#10004;' : '' }}</td>
                            <td>{{ $typ->repeat }}</td>
                            <td>{{ $typ->weekdayName }}</td>
                            <td>@if ( isset($type)  &&  $typ->id != $type->id)
                                    <a class="btn btn-sm btn-outline-info" href="{{ route('types.edit', $typ->id) }}">&#9997;</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>{{-- container --}}


    {{-- put focus on first input field 
    --}}
    <script>
        document.forms.inputForm.name.focus()

        @if (isset($type) && $type->generic)
            $('.other-stuff').toggle();
        @endif
        
    </script>

  

@stop