
    <!-- Modal to search for new song -->
    <form   id="searchSongForm" 
        action="{{url('cspot/items')}}" 
        method="POST" accept-charset="UTF-8" 
       enctype="multipart/form-data"
      onsubmit="return searchForSongs(this)">

        <div class="modal fade" id="searchSongModal" tabindex="-1" role="dialog" aria-labelledby="searchSongModalLabel" aria-hidden="true">

            <div class="modal-dialog" role="document">

                <div class="modal-content">



                    <div class="modal-header center">

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>

                        <h4 class="modal-title m-b-1">
                            <span id="searchSongModalLabel">Select what to insert</span> <span id="modal-show-item-id"></span>
                        </h4>

                        <a href="#" class="btn btn-lg btn-outline-primary modal-pre-selection m-b-1"
                            onclick="showModalSelectionItems('song')"      ><strong><i class="fa fa-music"></i> &nbsp; Song</strong></a>

                        <a href="#" class="btn btn-lg btn-outline-success modal-pre-selection m-b-1 font-weight-bold"
                            onclick="showModalSelectionItems('scripture')"><strong><i class="fa fa-book"></i> Scripture Text</strong></a>

                        <a href="#" class="btn btn-lg btn-outline-warning modal-pre-selection m-b-1"
                            onclick="showModalSelectionItems('clips')"><i class="fa fa-television"></i> VideoClip / InfoScreen</a>

                        <a href="#" class="btn btn-lg btn-outline-danger modal-pre-selection m-b-1"
                            onclick="showModalSelectionItems('file')"      ><i class="fa fa-file-picture-o"></i> &nbsp; Image</a>

                        <a href="#" class="btn btn-lg btn-outline-info modal-pre-selection m-b-1 font-weight-bold"
                            onclick="showModalSelectionItems('comment')"   ><i class="fa fa-comments"></i> Comment or Notes</a>

                    </div>



                    <div class="modal-body modal-select-comment modal-select-song modal-select-scripture modal-select-clips modal-select-file" style="display: none;">

                        {{-- comment --}}
                        <input type="text"   id="comment" name="comment"
                            class="center-block m-b-1 modal-select-comment modal-input-comment modal-select-scripture fully-width">


                        {{-- scripture --}}
                        <span class="modal-select-scripture">
                            @include( 'cspot.snippets.scripture_input', ['part' => 'one'] )
                            <br>
                            @include( 'cspot.snippets.scripture_input', ['part' => 'two'] )
                        </span>
                        

                        {{-- file/image --}}
                        <span class="modal-select-file">
                            @include( 'cspot.snippets.add_files', ['modal' => 'modal'] )
                        </span>


                        {{-- videoclip or infoscreen --}}
                        <label for="clips" class="search-form-item modal-select-clips m-b-0">Search titles:</label>
                        <input type="text" class="form-control search-form-item modal-select-clips modal-input-clips m-b-0" id="clips" onkeyup="showSongHints('#clipsHint', this.value)">
                        <div class="search-form-item modal-select-clips" id="clipsHint"></div>


                        {{-- song --}}
                        <label for="haystack" class="search-form-item modal-select-song m-b-0">Search Song title or number:</label>
                        <input type="text" class="form-control search-form-item modal-select-song modal-input-song m-b-0" id="haystack" onkeyup="showSongHints('#txtHint', this.value)">
                        <div class="search-form-item modal-select-song" id="txtHint"></div>

                        <label class="search-form-item modal-select-song m-t-1 m-b-0" for="MPselect">...or select Mission Praise number:</label>
                        <select class="form-control m-b-1 search-form-item modal-select-song" id="MPselect" onchange="$('#searchForSongsButton').click();">
                            <option value="0">select....</option>
                        </select>

                        <label id="search-action-label" class="center-block modal-select-song m-b-0">Full-text search incl. lyrics:</label>
                        <input type="text"   id="search-string" class="search-input search-form-item center-block modal-select-song">


                        <div id="search-result"></div>

                        <div id="searching" style="display: none;">
                            <i class="fa fa-spinner fa-pulse fa-lg fa-fw"></i>&nbsp;<span>leafing through the pages ...</span>
                        </div>


                        {{-- hidden data fields --}}
                        <input type="hidden" id="seq_no"        name="seq_no">
                        <input type="hidden" id="plan_id"       name="plan_id" data-search-url="{{ url('cspot/songs/search') }}">
                        <input type="hidden" id="beforeItem_id" name="beforeItem_id">
                        <input type="hidden" id="song_id"       name="song_id">
                        <input type="hidden" id="file_id"       name="file_id">
                        {{ csrf_field() }}

                    </div>



                    <div class="modal-footer">

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

