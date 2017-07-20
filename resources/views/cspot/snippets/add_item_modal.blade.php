
<!-- Modal to search for new song -->
<form   id="searchSongForm" 
    action="{{url('cspot/items')}}" 
    method="POST" accept-charset="UTF-8" 
   enctype="multipart/form-data"
  onsubmit="return searchForSongs()">

    <div class="modal fade" id="searchSongModal" tabindex="-1" role="dialog" aria-labelledby="searchSongModalLabel" aria-hidden="true">

        <div class="modal-dialog modal-lg" role="document">

            <div class="modal-content draggable" id="makeMeDraggable">


                {{-- M O D A L    H E A D E R - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
                --}}
                <div class="modal-body item-modal-header">One moment please....</div>
                
                <div class="modal-body item-modal-header center" style="display: none;">

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                    <h5 class="modal-title mb-1">
                        <span id="searchSongModalLabel">Select what to insert</span> <span id="modal-DONT-show-item-id"></span>
                    </h5>

                    <span class="btn btn-outline-primary modal-pre-selection link mb-1"
                        onclick="showModalSelectionItems('song')"      ><strong><i class="big fa fa-music"></i> &nbsp; Song</strong></span>

                    <span class="btn btn-outline-success modal-pre-selection link mb-1 font-weight-bold"
                        onclick="showModalSelectionItems('scripture')"><strong><i class="big fa fa-book"></i> Scripture Text</strong></span>

                    <span class="btn btn-outline-warning modal-pre-selection link mb-1"
                        onclick="showModalSelectionItems('clips')"><i class="big fa fa-television"></i> VideoClip / Slides</span>

                    <span class="btn btn-outline-danger modal-pre-selection link mb-1"
                        onclick="showModalSelectionItems('file')"      ><i class="big fa fa-file-picture-o"></i> &nbsp; Image</span>

                    <span class="btn btn-outline-info modal-pre-selection link mb-1 font-weight-bold"
                        onclick="showModalSelectionItems('comment')"   ><i class="big fa fa-comments"></i> Comment or Notes</span>

                </div>



                {{-- M O D A L    B O D Y - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
                --}}
                <div class="modal-body modal-select-comment modal-select-song modal-select-scripture modal-select-clips modal-select-file" style="display: none;">

                    {{-- comment 
                    --}}
                    <input type="text"   id="comment" name="comment"
                        class="center-block mb-1 modal-select-comment modal-input-comment modal-select-scripture fully-width">


                    {{-- scripture 
                    --}}
                    <span class="modal-select-scripture">
                        @include( 'cspot.snippets.scripture_input', ['part' => 'one'] )
                        <br>
                        @include( 'cspot.snippets.scripture_input', ['part' => 'two'] )
                    </span>
                    

                    {{-- file/image 
                    --}}
                    <span class="modal-select-file">
                        @include( 'cspot.snippets.add_files', ['modal' => 'modal'] )
                    </span>


                    {{-- videoclip or slides 
                    --}}
                    <label for="clips" class="search-form-item modal-select-clips mb-0">Search for titles of Slides or VideoClips:</label>
                    <input type="text" class="form-control search-form-item modal-select-clips modal-input-clips mb-0" 
                             id="clips" onkeyup="showSongHints('#clipsHint', this.value, 'clips')">
                    <div class="search-form-item modal-select-clips" id="clipsHint"></div>

                    <label class="search-form-item modal-select-clips mt-1 mb-0" for="ClipSelect">...or select from this list:</label>
                    <select class="form-control mb-1 search-form-item modal-select-clips" id="ClipSelect" onchange="$('#searchForSongsButton').click();">
                        <option value="0">select....</option>
                    </select>


                    {{-- song 
                    --}}
                    <label for="haystack" class="search-form-item modal-select-song mb-0">Search Song title or number:</label>
                    <input type="text" class="form-control search-form-item modal-select-song modal-input-song mb-0"
                             id="haystack" onkeyup="showSongHints('#txtHint', this.value)">
                    <div class="search-form-item modal-select-song" id="txtHint"></div>

                    <label class="search-form-item modal-select-song mt-1 mb-0" for="MPselect">...or select {{ env('CHURCH_HYMNBOOK_NAME') }} number:</label>
                    <select class="form-control mb-1 search-form-item modal-select-song" id="MPselect" onchange="$('#searchForSongsButton').click();">
                        <option value="0">select....</option>
                    </select>

                    <label id="search-action-label" class="center-block modal-select-song w-100 mb-0">Full-text search incl. lyrics:</label>
                    <input type="text"   id="search-string" class="search-input search-form-item center-block modal-select-song">



                    <div id="search-result"></div>


                    <div id="show-video-clip" style="display: none;">
                        <button type="button" class="close" aria-label="Close" onclick="$('#show-video-clip').hide();$('.search-result-items').toggle()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div></div>
                    </div>


                    <div id="searching" style="display: none;">
                        <i class="fa fa-spinner fa-pulse fa-lg fa-fw"></i>&nbsp;<span>leafing through the pages ...</span>
                    </div>


                    {{-- hidden data fields 
                    --}}
                    <input type="hidden" id="seq_no"        name="seq_no">
                    <input type="hidden" id="plan_id"       name="plan_id" data-search-url="{{ url('cspot/songs/search') }}">
                    <input type="hidden" id="beforeItem_id" name="beforeItem_id">
                    <input type="hidden" id="song_id"       name="song_id">
                    <input type="hidden" id="file_id"       name="file_id">
                    {{ csrf_field() }}
                    @if (isset($item))
                        <input type="hidden" id="item_id"   name="item_id" value="{{$item->id}}">
                    @Endif

                </div>



                <div class="modal-footer" style="display: none;">

                    <button class="btn btn-secondary modal-select-song modal-select-comment modal-select-scripture modal-select-clips modal-select-file"
                        type="button" onclick="resetSearchForSongs()">Restart</button>

                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="resetSearchForSongs()">Cancel</button>

                    <a href="#" class="btn btn-primary modal-select-song" id="searchForSongsButton" onclick="searchForSongs()">Search</a>

                    <button type="submit" class="btn btn-primary" id="searchForSongsSubmit">Submit</button>

                </div>



            </div>
        </div>
    </div>
</form>

<script>
    $('#makeMeDraggable').draggable();
</script>