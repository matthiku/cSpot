
{{--  
    Items Detail Page 

    comment input field 
 --}}

<div class="full-width">


    <div class="card" style="max-width: 40rem;">

        <div class="card-block">

            <h5 class="card-title"><i class="fa fa-sticky-note"> </i> Public Notes</h5>

            <p class="card-text">

                @if( Auth::user()->ownsPlan($plan->id) )
                    <pre id="comment-item-id-{{ $item->id }}" class="editable-item-field form-control form-control-success">{{ $item->comment }}</pre>

                @elseif (isset($item))

                    {!! Form::text('comment', $item->comment, ['disabled'=>'disabled']); !!}
                    <br>

                @endif

                @if (! $item->song_id)
                    {{-- checkbox to indicate if public note should be shown in the presenation --}}
                    <span class="btn btn-secondary pull-xs-left">
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" id="toggle-show-comment" 
                                  class="custom-control-input" {{ $item->show_comment ? 'checked="checked"' : '' }}
                                onclick="toggleShowComment(this, 'show_comment-item-id-{{ $item->id }}', '{{ route('cspot.api.item.update') }}')"
                                {{ Auth::user()->ownsPlan($plan->id) ? '' : ' disabled' }}>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description" id="show_comment-item-id-{{ $item->id }}"
                                >{{ $item->show_comment ? 'Notes are presented as Title' : 'Show notes as Title' }} in the presentation</span>
                        </label>
                    </span>
                @endif

                @if( Auth::user()->ownsPlan($plan->id) )
                    {{-- show link to clear the public note --}}
                    <a      href="#" class="card-link pull-xs-right form-control" id="public-notes-erase-link"
                            onclick="deleteItemNote('public', 'comment-item-id-{{ $item->id }}', '{{ route('cspot.api.item.update') }}')" 
                            style="max-width: 150px; display: {{ $item->comment ? 'initial' : 'none' }}">
                        <small><i class="fa fa-remove text-muted"></i> clear comment</small>
                    </a>
                @endif

            </p>

        </div>

    </div>


    {{-- ____________________________________

            show form for private notes 
        _____________________________________
    --}}
    <div class="card" style="max-width: 40rem;">
        <div class="card-block">

            <h5 class="card-title"><i class="fa fa-sticky-note-o"> </i> Private Notes</h5>
            <h6 class="card-subtitle text-muted">(only visible to you!)</h6>

            <p class="card-text">
                <pre id="notes-item-id-{{ $item->id }}" class="editable-item-field form-control form-control-success">{{ 
                    $item->itemNotes->where('user_id', Auth::user()->id)->first() ? $item->itemNotes->where('user_id', Auth::user()->id)->first()->text : '' }}</pre>
            </p>

            <a      href="#" class="card-link pull-xs-right form-control" id="private-notes-erase-link"  
                    onclick="deleteItemNote('private', 'notes-item-id-{{ $item->id }}', '{{ route('cspot.api.item.update') }}')" 
                    style="max-width: 150px; display: {{ $item->itemNotes->where('user_id', Auth::user()->id)->first() ? 'initial' : 'none' }}">
                <small><i class="fa fa-remove text-muted"></i> clear note</small>
            </a>

        </div>
    </div>





</div> 
