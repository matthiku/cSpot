
<td class="cell-part-text">


	<span class="toggle-onsong-buttons float-xs-right btn btn-sm" title="Show Edit Buttons" 
		onclick="
			if (! $('.show-onsong-format-hint').is(':visible'))
				if ($('.cell-part-action').is(':visible')) {
					$('.cell-part-action').hide();
					$('#insertNewOnSongRow-link').show();
				} else {
					$(this).siblings('.cell-part-action').show();
					$('.for-new-items').hide();
					$('#insertNewOnSongRow-link').hide();
				}		
		">&#9997;</span>


	{{-- OnSong Editor 
	--}}
	<div id="advanced-editor-{{ $newOnsongRow ? '0' : $onsong->id }}" class="advanced-editor rounded bg-info text-white lh-2 px-1"></div>


	{{-- Read-only area to show chords-over-lyrics 
	--}}
	<div class="white-space-pre-wrap lh-1 write-onsong-text{{ $newOnsongRow || $onsong->song_part->code!='m' ? ' show-onsong-text' : '' }}{{ $newOnsongRow ? ' hidden' : '' }}" 
		onclick="$(this).siblings('.toggle-onsong-buttons').click();" title="Click to edit">{{ $newOnsongRow ? '' : $onsong->text }}</div>


	{{-- text area to edit in plain onsong format 
	--}}
	<textarea class="plaintext-editor {{ $newOnsongRow ? 'new-onsong-field' : 'hidden' }} "          style="width: 100%; font-size: small;" 
		tabindex={{ $newOnsongRow ? '2' : '1' }} onkeyup="calculateTextAreaHeight(this);">{{ $newOnsongRow ? '' : $onsong->text }}</textarea>


	{{-- text area to edit in chords-over-lyrics format 
	--}}
	<textarea class="chords-over-lyrics-editor hidden" style="width: 100%; font-size: small;" 
		tabindex=1 onkeyup="calculateTextAreaHeight(this);"></textarea>


	<div class="error-msg hidden">Enter text here.</div>


	<div class="editor-hints small hidden">
		<span><span>			
		    	<small>Re-arrange the keys (chords) within the lyrics by dragging them left or right.</small>
		    	<button type="button" class="btn btn-sm float-xs-right btn-success"   onclick="submitEditedOnSong(   $(this))" >Save changes</button>
		    	<button type="button" class="btn btn-sm float-xs-right btn-secondary" onclick="cancelAdvOnSongEditor($(this))" >Cancel</button>
		</span></span>
	</div>


	<div class="text-editor-hints small hidden">

		<p class="card">

			<span class="float-xs-right">
				<a href="javascript:void(0);" onclick="saveNewOnSongText(this);" title="save"   tabindex=2 
				  class="btn btn-sm btn-success float-xs-right"><big>&#128427;</big><span class="hidden-sm-down"> Save &nbsp;</span></a>
				<br>
				<a href="javascript:void(0);" onclick="removeNewOnSongRow(this);" title="cancel" tabindex=3 
				  class="btn btn-sm btn-secondary">&#10007;<span class="hidden-sm-down">  Cancel</span></a>
			</span>

			Use "chords over lyrics" or OnSong-formatted chords with lyrics (lyrics with chords in square brackets).<br> 
			<small>(For more information, see the 
				<a href="http://www.onsongapp.com/docs/features/formats/onsong/chords/" target="new" class="text-info">
				OnSong manual on formats</a>)</small><br>
			<span class="text-danger">
				<strong>Note:</strong> Blank lines will force a new slide in lyrics presentations but will be ignored when showing the chords.
			</span>
		</p>
	</div>




	<div class="cell-part-action center small hidden">

		<span class="for-existing-items text-success">Choose your Editor:</span>
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

	</div>

</td>
