
{{--  
    Items Detail Page 

    comment input field 
 --}}

<div class="full-width">


    <div class="card mx-auto " style="max-width: 40rem;">

        <div class="card-block">

            <h5 data-item-update-action="{{ route('cspot.api.items.update', $item->id) }}"
                data-item-id="{{ $item->id }}" 
                class="card-title"><i class="fa fa-sticky-note"> </i> 

            {{-- is this item for leader's eyes only? --}}
            <a      href="#" class="float-right link small{{ $item->forLeadersEyesOnly ? ' bg-danger': '' }}" onclick="changeForLeadersEyesOnly(this)" 
                    data-value="{{ $item->forLeadersEyesOnly }}"
                    title="Make item visible for {{ $item->forLeadersEyesOnly ? 'everyone': "leader's eyes only (useful for personal notes etc.)" }}">
                @if ($item->forLeadersEyesOnly)
                    <i class="fa fa-eye-slash"></i>
                @else
                    <i class="fa fa-eye"></i>
                @endif
                <small style="display: {{ $item->forLeadersEyesOnly ? 'initial' : 'none' }}">(for Leader's eyes only!)</small>
                <small style="display: {{ $item->forLeadersEyesOnly ? 'none' : 'initial' }}">(visible to all)</small>
            </a>
            {{ $item->forLeadersEyesOnly ? "Leader's" : 'General' }} Notes</h5>




            @if( Auth::user()->ownsPlan($plan->id) )

                {{-- show link to clear the public note --}}
                <a      href="#" title="Discard notes text" 
                        class="btn btn-outline-secondary float-right item-comment-public" id="public-notes-erase-link"
                        onclick="deleteItemNote('public', 'comment-item-id-{{ $item->id }}', '{{ route('cspot.api.item.update') }}')" 
                        style="max-width: 150px; display: {{ $item->comment ? 'initial' : 'none' }}">
                    <i class="fa fa-remove text-muted"></i> erase
                </a>

                <pre id="comment-item-id-{{ $item->id }}" class="editable-item-field form-control form-control-success w-75 mb-0">{{ $item->comment }}</pre>

            @elseif (isset($item))

                {!! Form::text('comment', $item->comment, ['disabled'=>'disabled']); !!}
                <br>

            @endif


            @if (! $item->song_id)
                {{-- checkbox to indicate if public note should be shown in the presentation --}}
                <span class="btn btn-secondary float-left item-comment-public">
                    @include ('cspot.snippets.toggle-show-comment', ['label' => true])
                </span>
            @endif


            @if (! $item->song_id)
                {{-- checkbox to indicate if this item should be used to show the ANNOUNCEMENTS in the presentation --}}
                <br>
                <br>
                <span class="btn btn-secondary float-left item-comment-public">
                    <label class="custom-control custom-checkbox">
                        <input type="checkbox" id="toggle-show-announcements" 
                              class="custom-control-input" {{ $item->key == 'announcements' ? 'checked="checked"' : '' }}
                            onclick="toggleShowAnnouncement(this, 'key-item-id-{{ $item->id }}', '{{ route('cspot.api.item.update') }}')"
                            {{ Auth::user()->ownsPlan($plan->id) ? '' : ' disabled' }}>
                        <span class="custom-control-indicator"></span>
                        <span class="custom-control-description" id="key-item-id-{{ $item->id }}"
                            >{{ $item->key=='announcements' 
                                ? 'This item will show the announcements' 
                                : 'Use this item to show the announcements' }} in the presentation</span>
                    </label>
                </span>
            @endif

        </div>

    </div>


    {{-- ____________________________________

            show form for private notes 
        _____________________________________
    --}}
    <div class="card mx-auto mt-2 item-comment-public" style="max-width: 40rem;">
        <div class="card-block">

            <h5 class="card-title"><i class="fa fa-sticky-note-o"> </i> Private Notes</h5>
            <h6 class="card-subtitle text-muted">(only visible to you!)</h6>

            <p class="card-text">
                <pre id="notes-item-id-{{ $item->id }}" class="editable-item-field form-control form-control-success">{{ 
                    $item->itemNotes->where('user_id', Auth::user()->id)->first() ? $item->itemNotes->where('user_id', Auth::user()->id)->first()->text : '' }}</pre>
            </p>

            <a      href="#" class="card-link float-right form-control" id="private-notes-erase-link"  
                    onclick="deleteItemNote('private', 'notes-item-id-{{ $item->id }}', '{{ route('cspot.api.item.update') }}')" 
                    style="max-width: 150px; display: {{ $item->itemNotes->where('user_id', Auth::user()->id)->first() ? 'initial' : 'none' }}">
                <small><i class="fa fa-remove text-muted"></i> clear note</small>
            </a>

        </div>
    </div>




    {{-- _______________________________________________

          Option to re-arrange item within the plan  
        ________________________________________________
    --}}
    <div class="card mx-auto mt-2 item-rearrange" style="max-width: 40rem;">
        <div class="card-block">

            <h5 class="card-title rearrange-item-title">Re-Arrange this Item:</h5>

            <p class="card-text">

                <div class="btn-group mt-0" data-toggle="buttons">
                    <label class="btn btn-outline-primary" onclick="$('.rearrange-select-item').removeAttr('disabled');">
                        <input type="radio" name="before-or-after" id="before" autocomplete="off"> before
                    </label>
                    <label class="btn btn-outline-primary" onclick="$('.rearrange-select-item').removeAttr('disabled');">
                        <input type="radio" name="before-or-after" id="after"  autocomplete="off"> after
                    </label>
                     
                    <select class="rearrange-select-item custom-select angled-left" disabled onchange="$('.rearrange-submit-button').removeAttr('disabled');">
                        <option selected>(select item ...)</option>
                        @foreach ($items as $selItem)
                            @if($selItem->id != $item->id)
                                <option value="{{ $selItem->seq_no }}">{{ $selItem->seq_no }}
                                    @php 
                                        if ($selItem->song_id && $selItem->song->title) {
                                            if ( $selItem->song->title_2=='slides' || $selItem->song->title_2=='video' )
                                                $text = '(' . ucfirst($selItem->song->title_2) . ') ' . $selItem->song->title;
                                            else
                                                $text = '&#127926;' . $selItem->song->title; 
                                        } else
                                            $text = $selItem->comment;
                                        echo substr($text, 0, 45);
                                    @endphp
                                </option>
                            @endif
                        @endforeach
                    </select>

                </div>

                <button type="button" disabled onclick="rearrangeItem();" 
                    class="rearrange-submit-button btn btn-primary btn-sm btn-block">Submit</button>
            </p>

        </div>
    </div>





</div> 

@if ($item->forLeadersEyesOnly)
    <script>
        $('.item-comment-public').hide();
    </script>
@endif