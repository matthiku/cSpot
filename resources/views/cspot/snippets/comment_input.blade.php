
<!-- 
    Items Detail Page 

    comment input field 
-->

<div class="full-width">
    {!! Form::label('comment', 'Comments or notes', ['id'=>'comment-label']); !!}
    <p>
        @if( Auth::user()->ownsPlan($plan->id) )
            {!! Form::text('comment'); !!}
        @elseif (isset($item))
            {!! Form::text('comment', $item->comment, ['disabled'=>'disabled']); !!}
        @endif
        @if ($errors->has('comment'))
            <br><span class="help-block">
                <strong>{{ $errors->first('comment') }}</strong>
            </span>
        @endif
    </p>
    {{-- private notes only for leader --}}
    @if (Auth::user()->id == $plan->leader_id)
        {!! Form::label('priv_comment', 'Private notes', ['id'=>'comment-label']); !!}
        (only visible to you!)
        <p>{!! Form::textarea( 'priv_comment'); !!}</p>
    @endif
</div> 
