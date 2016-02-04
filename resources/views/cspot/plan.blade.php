
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Create or Update a Service Plan")

@if (isset($plan))
    @section('plans', 'active')
@else
    @section('create', 'active')
@endif



@section('content')


    @include('layouts.flashing')


    @if (isset($plan))
        <h2>Plan for "{{ $plan->type->name }}" on {{ $plan->date->formatLocalized('%A, %d %B %Y') }}</h2>
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
        <h2>Add Service Plan</h2>
        {!! Form::open(array('action' => 'Cspot\PlanController@store', 'id' => 'inputForm')) !!}
    @endif



    <div class="row center">
        <div class="col-lg-3 col-md-6">
            <div class="row form-group">
                {!! Form::label('date', 'Date'); !!}
                @if (Auth::user()->isEditor())
                    {!! Form::date( 'date', isset($plan) ? $plan->date : \Carbon\Carbon::now() ) !!}
                @else
                    {!! Form::date( 'date', isset($plan) ? $plan->date : \Carbon\Carbon::now(), ['disabled' => 'disabled'] ) !!}
                @endif
            </div>
        </div>                    


        <div class="col-lg-3 col-md-6">
            <div class="row form-group">
                <label>Type of Service</label>                 
                <select name="type_id" class="c-select"{{ Auth::user()->isEditor() ? '' : ' disabled' }}>
                    <option {{ isset($plan) ? '' : 'selected'}}>
                        Select ...
                    </option>
                    @foreach ($types as $type)
                        <option 
                            @if( ( ''<>old('type_id') && $type->id==old('type_id') )  ||  isset($plan) && $plan->type_id==$type->id )
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

        <div class="col-lg-3 col-md-6">
            <div class="row form-group">
                <label>Leader </label>
                <select name="leader_id" class="c-select"{{ Auth::user()->isEditor() ? '' : ' disabled' }}>
                    <option {{ isset($plan) ? '' : 'selected'}}>
                        Select ...
                    </option>
                    @foreach ($users as $user)
                        @if( $user->hasRole('leader'))
                            <option 
                                @if( ( ''<>old('leader_id') && $user->id==old('leader_id') )  ||  isset($plan) && $plan->leader_id==$user->id )
                                    selected
                                @endif
                                value="{{ $user->id }}">{{ $user->first_name }}</option>
                        @endif
                    @endforeach
                </select>
                @if ($errors->has('leader_id'))
                    <br><span class="help-block">
                        <strong>{{ $errors->first('leader_id') }}</strong>
                    </span>
                @endif
            </div>
        </div>                    

        <div class="col-lg-3 col-md-6">
            <div class="row form-group">
                <label>Teacher</label>
                <select name="teacher_id" class="c-select"{{ Auth::user()->isEditor() ? '' : ' disabled' }}>
                    <option {{ isset($plan) ? '' : 'selected'}}>
                        Select ...
                    </option>
                    <option value="0">None</option>
                    @foreach ($users as $user)
                        @if( $user->hasRole('teacher'))
                            <option 
                                @if( ( ''<>old('teacher_id') && $user->id==old('teacher_id') )  ||  isset($plan) && $plan->teacher_id==$user->id )
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
            </div>
        </div>
    </div>



    @if (isset($plan))
        <!-- Show items for existing plan -->

        @include('cspot.items')

    @else

        <!-- Checkbox to add default items into NEW plan -->
        <input type="hidden" name="defaultItems" value="false">
        <div class="checkbox xs-center">
          <label>
            <input checked="checked" type="checkbox" value="Y" name="defaultItems">
            Insert default items for this plan?
          </label>
        </div>                

    @endif

    <div class="form-buttons xs-center">
        &nbsp; &nbsp; 
        @if (isset($plan))

            @if (Auth::user()->isEditor())            
                &nbsp; {!! Form::submit('Save changes'); !!}
                <script type="text/javascript">document.forms.inputForm.date.focus()</script>
            @else
                &nbsp; {!! Form::submit('Save Note'); !!}
                <script type="text/javascript">document.forms.inputForm.info.focus()</script>
            @endif

            @if (Auth::user()->isAdmin())
                &nbsp; <a class="btn btn-danger btn-sm"  plan="button" href="{{ url('cspot/plans/'.$plan->id) }}/delete">
                    <i class="fa fa-trash" > </i> &nbsp; Delete
                </a>
            @endif

        @else
            &nbsp; <input class="xs-widht-half" type="submit" value="Submit">

            <script type="text/javascript">document.forms.inputForm.date.focus()</script>
        @endif

        &nbsp; <a href="{{ url('cspot/plans/future') }}">{!! Form::button('Back'); !!}</a>
    </div>
    

    <div class="form-group">
        <br>
        @if (Auth::user()->isEditor())
            {!! Form::label('info', 'Notes:'); !!}
            <br/>
            {!! Form::textarea('info') !!}
        @else
            @if ($plan->info)
                <h5>Notes for this Plan:</h5>
                <pre>{!! $plan->info !!}</pre>
            @endif
            Add note:
            <textarea name="info"></textarea>
        @endif
    </div>



    {!! Form::close() !!}

    
@stop