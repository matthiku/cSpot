
@extends('layouts.main')


@section('content')

<h3>Create a new message</h3>

{!! Form::open(['route' => 'messages.store']) !!}

<div class="col-md-6">

    <!-- Recipient -->
    <div class="form-group">
        {!! Form::label('recipients[]', 'Recipient(s)', ['class' => 'control-label']) !!}
        @if($users->count() > 0)
        <div class="checkbox">
            @foreach($users as $user)
                @if ($user->isUser())
                    <label title="{!!$user->getFullname()!!}" class="m-r-1">
                        <input type="checkbox" name="recipients[]" value="{!!$user->id!!}">
                        {!!$user->name!!}
                    </label>
                @endif
            @endforeach
        </div>
        @endif
    </div>

    <!-- Subject Form Input -->
    <div class="form-group">
        {!! Form::label('subject', 'Subject', ['class' => 'control-label']) !!}
        {!! Form::text('subject', null, ['class' => 'form-control']) !!}
    </div>

    <!-- Message Form Input -->
    <div class="form-group">
        {!! Form::label('message', 'Message', ['class' => 'control-label']) !!}
        {!! Form::textarea('message', null, ['class' => 'form-control']) !!}
    </div>
    
    <!-- Submit Form Input -->
    <div class="form-group">
        {!! Form::submit('Submit', ['class' => 'btn btn-primary form-control']) !!}
    </div>
</div>

{!! Form::close() !!}

@stop
