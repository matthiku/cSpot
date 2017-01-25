
@extends('layouts.main')


@section('content')

    <div class="col-md-6">



        <h3>{!! $thread->subject !!}</h3>
@php
    //dd($thread->participants->first()->last_read);
@endphp

        @foreach($thread->messages as $message)
            <div class="media">
                <a class="float-left mr-1" href="#">
                    <img src="//www.gravatar.com/avatar/{!! md5($message->user->email) !!}?s=64" alt="{!! $message->user->name !!}" class="img-circle">
                </a>


                <div class="media-body mb-2">

                    <a class="btn btn-warning btn-sm float-right" data-toggle="tooltip" title="Remove" 
                        href='{{ url('messages/'.$message->id) }}/delete'><i class="fa fa-trash"></i></a>

                    <h5 class="media-heading">From: {!! $message->user->name !!}</h5>

                    <pre class="bg-info rounded text-white mb-0">{!! $message->body !!}</pre>

                    <div class="text-muted"><small>(Posted {!! $message->created_at->diffForHumans() !!})</small></div>
                </div>


            </div>
        @endforeach




        <h4>Reply to this message</h4>

        {!! Form::open(['route' => ['messages.update', $thread->id], 'method' => 'PUT']) !!}

            <!-- Message Form Input -->
            <div class="form-group">
                {!! Form::textarea('message', null, ['class' => 'form-control']) !!}
            </div>


            <p class="small">
                <strong>Current Participants:</strong> -
                @foreach($thread->participants()->get() as $participant)
                    @if (Auth::user()->id != $participant->user_id)
                        {{ $participant->user->name }} 
                        {!! $thread->isUnread($participant->user_id) 
                            ? '<span title="unread">&#128213;</span>' 
                            : '&#128214; ('.($participant->last_read ? $participant->last_read->diffForHumans() : '?').') -' !!}
                    @else
                        You -
                    @endif
                @endforeach
            </p>
            @php 
                //dd($thread->participants()->get()); 
            @endphp

            <!-- Recipient -->
            <div class="form-group">
                {!! Form::label('recipients[]', 'Add Recipient(s)', ['class' => 'control-label']) !!}
                @if($users->count() > 0)
                <div class="checkbox">
                    @foreach($users as $user)
                        @if ($user->isUser())
                            <label title="{!! $user->getFullname !!}" class="mr-1">
                                <input type="checkbox" name="recipients[]" value="{!!$user->id!!}">
                                {!!$user->name!!}
                            </label>
                        @endif
                    @endforeach
                </div>
                @else 
                    no other users found!
                @endif
            </div>

            <!-- Submit Form Input -->
            <div class="form-group">
                {!! Form::submit('Submit', ['class' => 'btn btn-primary form-control']) !!}
            </div>

        {!! Form::close() !!}

    </div>

@stop