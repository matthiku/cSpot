
{{--  
    Items Detail Page 

    comment input field 
 --}}

<div class="full-width">


    @if (isset($item))    

        <div class="card">

            <div class="card-block">

                <h5 class="card-title"><i class="fa fa-sticky-note"> </i> Public Comments</h5>

                <p class="card-text">

                    @if( Auth::user()->ownsPlan($plan->id) )
                        <pre id="comment-item-id-{{ $item->id }}" class="editable-item-field">{{ $item->comment }}</pre>

                    @elseif (isset($item))

                        {!! Form::text('comment', $item->comment, ['disabled'=>'disabled']); !!}

                    @endif

                </p>

                <a      href="#" class="card-link" onclick="deletePublicItemNote('comment-item-id-{{ $item->id }}')" id="public-notes-erase-link"
                        style="display: {{ $item->comment ? 'initial' : 'none' }}">
                    <small><i class="fa fa-remove text muted"></i> clear comment</small></a>
            </div>

        </div>


    @else


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


    @endif



    {{-- private notes only visible for user --}}

    @if (isset($item))    


        <div class="card">

            <div class="card-block">

                <h5 class="card-title"><i class="fa fa-sticky-note-o"> </i> Private Notes</h5>
                <h6 class="card-subtitle text-muted">(only visible to you!)</h6>

                <p class="card-text">
                    <pre id="notes-item-id-{{ $item->id }}" class="editable-item-field">{{ 
                        $item->itemNotes->where('user_id', Auth::user()->id)->first() ? $item->itemNotes->where('user_id', Auth::user()->id)->first()->text : '' }}</pre>
                </p>

                <a      href="#" class="card-link" onclick="deletePrivateItemNote('notes-item-id-{{ $item->id }}')" id="private-notes-erase-link"
                        style="display: {{ $item->itemNotes->where('user_id', Auth::user()->id)->first() ? 'initial' : 'none' }}">
                    <small><i class="fa fa-remove text muted"></i> remove note</small></a>
            </div>

        </div>


    @endif





</div> 
