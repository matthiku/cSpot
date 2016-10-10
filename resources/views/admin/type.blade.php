
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Create or Update a User Type")

@section('types', 'active')



@section('content')


    @include('layouts.flashing')


    <a class="btn btn-outline-warning pull-xs-right" 
            href="{{ url('cspot/plans/create') }}?type_id={{ $type->id }}"
            title="Create a new Event of this type">
        <i class="fa fa-plus"> </i> Create Event
    </a>



    @if (isset($type))
        <h2>Update Type</h2>
        {!! Form::model( $type, array('route' => array('types.update', $type->id), 'method' => 'put', 'id' => 'inputForm') ) !!}
    @else
        <h2>Create Type</h2>
        {!! Form::open(array('action' => 'Admin\TypeController@store', 'id' => 'inputForm')) !!}
    @endif



    <p>{!! Form::label('name', 'Type Name'); !!}<br>
       {!! Form::text('name'); !!}
        @if ($errors->has('name'))
            <br><span class="help-block">
                <strong>{{ $errors->first('name') }}</strong>
            </span>
        @endif
    </p>


    <p>{!! Form::label('subtitle', 'Subtitle or Info or Location'); !!}<br>
       {!! Form::text('subtitle'); !!}</p>


    <p>{!! Form::label('start', 'Usual start'); !!}<br>
       {!! Form::time('start'); !!}</p>


    <p>{!! Form::label('end', 'Usual end'); !!}<br>
       {!! Form::time('end'); !!}</p>


    <p>{!! Form::label('repeat', 'Interval (repeat)'); !!}<br>
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
    </p>


    <p>{!! Form::label('weekday', 'Weekday'); !!}<br>
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
    </p>


    <p>{!! Form::label('leader_id', 'Default Leader'); !!}<br>
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
    </p>


    <p>{!! Form::label('resource_id', 'Default Resource'); !!}<br>
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



    @if (isset($type))
        <p>{!! Form::submit('Update'); !!}</p>
        <hr>
        <a class="btn btn-danger btn-sm"  type="button" href="{{ url('admin/types/'.$type->id) }}/delete">
            <i class="fa fa-trash" > </i> &nbsp; Delete
        </a>
    @else
        <p>{!! Form::submit('Submit'); !!}
    @endif



    <a href="{{url('admin/types')}}">{!! Form::button('Cancel'); !!}</a></p>
    {!! Form::close() !!}


    <script type="text/javascript">document.forms.inputForm.name.focus()</script>

    
@stop