
    <!-- Modal to search for new song -->
    <form id="searchSongForm" action="{{url('cspot/items')}}" method="POST">
        <div class="modal fade" id="searchSongModal" tabindex="-1" role="dialog" aria-labelledby="searchSongModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title m-b-1">
                            <span id="searchSongModalLabel">Select what to insert</span> <span id="modal-show-item-id"></span>
                        </h4>

                        <a href="#" class="btn btn-lg btn-outline-primary modal-pre-selection fully-width m-b-1"
                            onclick="showModalSelectionItems('song')"       ><strong>Song</strong> <small>(or videoclip or infoscreen)</small></a>
                        <a href="#" class="btn btn-lg btn-outline-success modal-pre-selection fully-width m-b-1"
                            onclick="showModalSelectionItems('scripture')"  >Scripture  </a>
                        <a href="#" class="btn btn-lg btn-outline-info modal-pre-selection fully-width m-b-1"
                            onclick="showModalSelectionItems('comment')"    >Comment or Notes</a>
                    </div>

                    <div class="modal-body modal-select-comment modal-select-song modal-select-scripture" style="display: none;">
                        <input type="text"   id="comment" name="comment"
                            class="center-block m-b-1 modal-select-comment modal-input-comment modal-select-scripture fully-width">

                        <span class="modal-select-scripture">
                            @include( 'cspot.snippets.scripture_input', ['part' => 'one'] )
                            <br>
                            @include( 'cspot.snippets.scripture_input', ['part' => 'two'] )
                        </span>

                        <label for="haystack" class="search-form-item modal-select-song m-b-0">Search Song title or number:</label>
                        <input type="text" class="form-control search-form-item modal-select-song modal-input-song m-b-0" id="haystack" onkeyup="showHint(this.value)">
                        <div class="search-form-item modal-select-song" id="txtHint"></div>

                        <label class="search-form-item modal-select-song m-t-1 m-b-0" for="MPselect">...or select Mission Praise number:</label>
                        <select class="form-control m-b-1 search-form-item modal-select-song" id="MPselect" onchange="$('#searchForSongsButton').click();">
                            <option value="0">select....</option>
                            {{-- only add MP songs --}}
                            @foreach ($mp_song_list as $song){!!substr($song->book_ref,0,2)=='MP' ? '<option value="'.$song->id.'">'.$song->number.' - '.$song->title.'</option>' : ''!!}@endforeach
                        </select>

                        <label id="search-action-label" class="center-block modal-select-song m-b-0">Full-text search incl. lyrics:</label>
                        <input type="text"   id="search-string" class="search-input search-form-item center-block modal-select-song">

                        <input type="hidden" id="seq-no">
                        <input type="hidden" id="plan_id"       name="plan_id" data-search-url="{{ url('cspot/songs/search') }}">
                        <input type="hidden" id="beforeItem_id" name="beforeItem_id">
                        <input type="hidden" id="song_id"       name="song_id">
                        {{ csrf_field() }}

                        <div id="search-result"></div>
                        <div id="searching" style="display: none;">
                            <i class="fa fa-spinner fa-pulse fa-lg fa-fw"></i>&nbsp;<span>leafing through the pages ...</span>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary modal-select-song modal-select-comment modal-select-scripture" 
                            type="button" onclick="resetSearchForSongs()">Restart</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="resetSearchForSongs()">Cancel</button>
                        <a href="#" class="btn btn-primary modal-select-song" id="searchForSongsButton" onclick="searchForSongs()">Search</a>
                        <button type="submit" class="btn btn-primary" 
                            id="searchForSongsSubmit" onclick="searchForSongs(this)">Submit</button>
                    </div>

                </div>
            </div>
        </div>
    </form>

