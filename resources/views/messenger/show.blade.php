
@extends('layouts.main')


@section('content')

    <div class="col-md-6">



        <h3>{!! $thread->subject !!}</h3>

        @foreach($thread->messages as $message)
            <div class="media">
                <a class="pull-left m-r-1" href="#">
                    <img src="//www.gravatar.com/avatar/{!! md5($message->user->email) !!}?s=64" alt="{!! $message->user->name !!}" class="img-circle">
                </a>
                <div class="media-body">
                    <a class="btn btn-warning btn-sm pull-xs-right" data-toggle="tooltip" title="Remove" 
                        href='{{ url('messages/'.$message->id) }}/delete'><i class="fa fa-trash"></i></a>
                    <h5 class="media-heading">{!! $message->user->name !!}</h5>
                    <p>{!! $message->body !!}</p>
                    <div class="text-muted"><small>Posted {!! $message->created_at->diffForHumans() !!}</small></div>
                </div>
            </div>
        @endforeach



        <h4>Reply to this message</h4>

        {!! Form::open(['route' => ['messages.update', $thread->id], 'method' => 'PUT']) !!}

            <!-- Message Form Input -->
            <div class="form-group">
                {!! Form::textarea('message', null, ['class' => 'form-control']) !!}
            </div>


            <p><small><strong>Current Participants:</strong> {!! $thread->participantsString(  ) !!}</small></p>

            <!-- Recipient -->
            <div class="form-group">
                {!! Form::label('recipients[]', 'Add Recipient(s)', ['class' => 'control-label']) !!}
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