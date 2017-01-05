

<span class="toggle-onsong-buttons float-xs-right btn btn-sm" title="Show Edit Buttons" 
	onclick="$(this).parent().children('.cell-part-action').toggle();">&#9997;</span>


<div id="advanced-editor-{{ $newOnsongRow ? '0' : $onsong->id }}" class="advanced-editor rounded bg-info text-white lh-2 px-1"></div>


<div class="white-space-pre-wrap lh-1 write-onsong-text{{ $newOnsongRow || $onsong->song_part->code!='m' ? ' show-onsong-text' : '' }}{{ $newOnsongRow ? ' hidden' : '' }}" 
	onclick="editOnSongText(this);" title="Click to edit">{{ $newOnsongRow ? '' : $onsong->text }}</div>


<textarea class="plaintext-editor {{ $newOnsongRow ? 'new-onsong-field' : 'hidden' }} "          style="width: 100%; font-size: small;" 
	tabindex={{ $newOnsongRow ? '2' : '1' }} onkeyup="calculateTextAreaHeight(this);">{{ $newOnsongRow ? '' : $onsong->text }}</textarea>


<textarea class="chords-over-lyrics-editor hidden" style="width: 100%; font-size: small;" 
	tabindex=1 onkeyup="calculateTextAreaHeight(this);"></textarea>


<div class="error-msg hidden">Enter text here.</div>


<div class="editor-hints hidden small">
    <small>Re-arrange the keys (chords) within the lyrics by dragging them left or right.</small>
    <button type="button" class="btn btn-sm float-xs-right btn-success"onclick="submitEditedOnSong(this)" >Save changes</button>
    <button type="button" class="btn btn-sm float-xs-right btn-secondary"onclick="cancelAdvOnSongEditor(this)" >Cancel</button>
</div>




<div class="cell-part-action center hidden small">

	<span class="for-existing-items">Choose your Editor:</span>

	@if ( isset($onsong) && $onsong->song_part->code!='m')
		<button title="Advanced OnSong Editor" type="button" class="btn btn-outline-primary btn-sm for-existing-items" onclick="fillAdvOnSongEditor(this)">OnSong</button>
	@endif

	<button title="Plain OnSong-Text Editor" type="button" class="btn btn-outline-primary btn-sm for-existing-items" onclick="editOnSongText($(this).parent())">Plain text</button>

	<button title="Chords-Over-Lyrics Editor" type="button" class="btn btn-outline-primary btn-sm for-existing-items" onclick="editOnSongLyrics(this)">Chords-Over-Lyrics</button>



	<span class="float-xs-right for-existing-items">delete
		<a href="javascript:void(0);" onclick="deleteOnSongText(this);" title="delete this part" 
		  class="btn btn-outline-danger btn-sm"><big>&#128465;</big>
	  	</a>
	</span>



	<a href="javascript:void(0);" onclick="saveNewOnSongText(this);" style="display: none;" title="save"   tabindex=2 
	  class="btn btn-outline-primary btn-sm for-new-items"><big>&#128427;</big></a>

	<a href="javascript:void(0);" onclick="removeNewOnSongRow(this);" style="display: none;" title="cancel" tabindex=3 
	  class="btn btn-outline-secondary btn-sm for-new-items">&#10007;</a>

</div>
