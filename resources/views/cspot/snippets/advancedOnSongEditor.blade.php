
<!-- Modal for advanced OnSong Editor 
-->
<div class="modal fade" id="advOnSongEditor" tabindex="-1" role="dialog" aria-labelledby="advOnSongEditorLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">


            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="advOnSongEditorLabel">Advanced OnSong Chords Editor</h4>
            </div>


            <div class="modal-body">
                <small>Re-arrange the keys (chords) within the lyrics by dragging them left or right.</small>
                <div id="advOnSongEditorArea" class="rounded bg-info text-white lh-2 px-1"></div>
            </div>


            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary"onclick="submitEditedOnSong()" >Save changes</button>
            </div>


        </div>
    </div>
</div>
