

<span class="for-existing-items">Choose your Editor:</span>

@if ( isset($onsong) && $onsong->song_part->code!='m')
	<button title="Advanced OnSong Editor" type="button" class="btn btn-outline-primary btn-sm for-existing-items" onclick="fillAdvOnSongEditor(this)">OnSong</button>
@endif

<button title="Plain Text Editor" type="button" class="btn btn-outline-primary btn-sm for-existing-items" onclick="editOnSongText($(this).parent())">Plain text</button>

<button title="Lyrics Editor" type="button" class="btn btn-outline-primary btn-sm for-existing-items" onclick="editOnSongLyrics(this)">Lyrics</button>



<span class="float-xs-right for-existing-items">delete
	<a href="javascript:void(0);" onclick="deleteOnSongText(this);" title="delete this part" 
	  class="btn btn-outline-danger btn-sm"><big>&#128465;</big>
  	</a>
</span>



<a href="javascript:void(0);" onclick="saveNewOnSongText(this);" style="display: none;" title="save"   tabindex=2 
  class="btn btn-outline-primary btn-sm for-new-items"><big>&#128427;</big></a>

<a href="javascript:void(0);" onclick="removeNewOnSongRow(this);" style="display: none;" title="cancel" tabindex=3 
  class="btn btn-outline-secondary btn-sm for-new-items">&#10007;</a>
