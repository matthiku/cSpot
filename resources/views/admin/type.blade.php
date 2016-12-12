
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Create or Update a User Type")

@section('types', 'active')



@section('content')


    @include('layouts.flashing')


    <div class="container">
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

            <div class="col-sm-2">
                @if (isset($type))
                    <a class="btn btn-outline-primary float-sm-right" 
                            href="{{ url('cspot/plans/create') }}?type_id={{ $type->id }}"
                            title="Create a new Event of this type">
                        <i class="fa fa-plus"> </i> Create new "{{ $type->name }}" Event
                    </a>
                @endif
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
                <div class="btn btn-secondary float-xs-left mr-1" onclick="changeGeneric(this);">
                    {!! Form::hidden('generic', '0') !!}
                    {!! Form::checkbox('generic', '1') !!}
                </div>    
                <div class="small narrow"><strong>Note:</strong> A generic event type has no default values and can be used for all kinds of events. 
                When creating a new event of that type, the new subtitle will be used as the main title (or name) of the actual event.</div>
           </div>
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
                <div class="float-sm-right">

                    @if (isset($type))
                        {!! Form::submit('&#10003; Update', ['class'=>'btn btn-outline-success submit-button disabled']); !!}
                    @else
                        {!! Form::submit('&#10004; Submit', ['class'=>'btn btn-outline-success submit-button disabled']); !!}
                    @endif

                </div>
            </div>
            <div class="col-sm-6">

                @if (isset($type) && $type->plans->count() == 0)
                    <a class="btn btn-danger float-xs-right" href="{{ url('admin/types/'.$type->id) }}/delete">
                        <i class="fa fa-trash" > </i> &nbsp; Delete
                    </a>
                @endif

                <a href="{{url('admin/types')}}">{!! Form::button('&#10008; Cancel', ['class'=>'btn btn-secondary cancel-button']); !!}</a></p>

           </div>
        </div>
        
        {!! Form::close() !!}

    </div>{{-- container --}}


    {{-- put focus on first input field 
    --}}
    <script type="text/javascript">
        document.forms.inputForm.name.focus()

        @if ($type->generic)
            $('.other-stuff').hide();
        @endif

        function changeGeneric(that){
            $(that).children('input')[1].click();
            if ( $($(that).children('input')[1]).prop('checked')==true )
                $('.other-stuff').hide();
            else
                $('.other-stuff').show();                
        }

    </script>

  

@stop