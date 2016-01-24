@extends('layouts.main')

@section('title', "Create or Update a Plan")

@if (isset($plan))
    @section('plans', 'active')
@else
    @section('create', 'active')
@endif



@section('content')

    @include('layouts.sidebar')

    @include('layouts.flashing')


    @if (isset($plan))
        <h2>Update Service Plan for {{ $plan->date->formatLocalized('%A, %d %B %Y') }}</h2>
        {!! Form::model( $plan, array(
            'route'  => array('cspot.plans.update', $plan->id), 
            'method' => 'put', 
            'id'     => 'inputForm',
            'class'  => 'form-horizontal'
            )) !!}
    @else
        <h2>Add a new Service Plan</h2>
        {!! Form::open(array('action' => 'Cspot\PlanController@store', 'id' => 'inputForm')) !!}
    @endif



        <div class="row">

                
                <div class="row form-group">
                    {!! Form::label('date', 'Date'); !!}<br/>
                    {!! Form::date( 'date', isset($plan) ? $plan->date : \Carbon\Carbon::now() ) !!}
                </div>


                <div class="row form-group">
                    <label>Type of Service</label><br/>
                    <select name="type_id" class="c-select">
                        <option {{ isset($plan) ? '' : 'selected'}}>
                            Select ...
                        </option>
                        @foreach ($types as $type)
                            <option 
                                @if( $type->id==old('type_id') || isset($plan) && $plan->type_id==$type->id )
                                    selected
                                @endif
                                value="{{ $type->id }}">{{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('type_id'))
                        <span class="help-block">
                            <strong>{{ $errors->first('type_id') }}</strong>
                        </span>
                    @endif
                </div>

                
                <div class="row form-group">
                    <label>Leader </label><br/>
                    <select name="leader_id" class="c-select">
                        <option {{ isset($plan) ? '' : 'selected'}}>
                            Select ...
                        </option>
                        @foreach ($users as $user)
                            @if( $user->hasRole('leader'))
                                <option 
                                    @if( $user->id==old('leader_id') || isset($plan) && $plan->leader_id==$user->id )
                                        selected
                                    @endif
                                    value="{{ $user->id }}">{{ $user->first_name }}</option>
                            @endif
                        @endforeach
                    </select>
                    @if ($errors->has('leader_id'))
                        <span class="help-block">
                            <strong>{{ $errors->first('leader_id') }}</strong>
                        </span>
                    @endif
                </div>

                
                <div class="row form-group">
                    <label>Teacher</label><br/>
                    <select name="teacher_id" class="c-select">
                        <option {{ isset($plan) ? '' : 'selected'}}>
                            Select ...
                        </option>
                        <option value="0">None</option>
                        @foreach ($users as $user)
                            @if( $user->hasRole('teacher'))
                                <option 
                                    @if( $user->id==old('teacher_id') || isset($plan) && $plan->teacher_id==$user->id )
                                        selected
                                    @endif
                                    value="{{ $user->id }}">{{ $user->first_name }}</option>
                            @endif
                        @endforeach
                    </select>
                    @if ($errors->has('teacher_id'))
                        <span class="help-block">
                            <strong>{{ $errors->first('teacher_id') }}</strong>
                        </span>
                    @endif
                </div>


                <div class="row form-group">
                    {!! Form::label('info', 'Notes'); !!}<br/>
                    {!! Form::textarea('info'); !!}
                </div>


        </div>




        @if (isset($plan))
            {!! Form::submit('Save changes'); !!}

            @if (Auth::user()->isAdmin())
                <a class="btn btn-danger btn-sm"  plan="button" href="/cspot/plans/{{ $plan->id }}/delete">
                    <i class="fa fa-trash" > </i> &nbsp; Delete
                </a>
            @endif
        @else
            {!! Form::submit('Submit'); !!}
        @endif
        <a href="/cspot/plans/future">{!! Form::button('Cancel'); !!}</a>

    {!! Form::close() !!}


    <script type="text/javascript">document.forms.inputForm.date.focus()</script>

    
@stop