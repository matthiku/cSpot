
<!-- 
    Items Detail Page 

    comment input field 
-->

<div class="full-width">
    {!! Form::label('comment', 'Comments or notes', ['id'=>'comment-label']); !!}
    <p onclick="blink('.save-buttons')">
        @if( Auth::user()->ownsPlan($plan->id) )
            {!! Form::text('comment'); !!}
        @else
            {!! Form::text('comment', $item->comment, ['disabled'=>'disabled']); !!}
        @endif
        @if ($errors->has('comment'))
            <br><span class="help-block">
                <strong>{{ $errors->first('comment') }}</strong>
            </span>
        @endif
    </p>
</div> 
