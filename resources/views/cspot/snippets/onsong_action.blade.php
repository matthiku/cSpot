

{{-- 
	table cell to show the actual OnSong data, provide the editors and all relevant action buttons 
--}}


<div class="cell-part-text">

	@if (Auth::user()->isEditor())
		<span class="toggle-onsong-buttons float-right btn btn-sm" title="Show Edit Buttons" 
			onclick="toggleOnSongEditButtons(this);">&#9997;</span>
	@endif



	<div class="cell-part-action float-right small hidden">

		<span class="float-right for-existing-items">
			<a href="javascript:void(0);" onclick="deleteOnSongText(this);" title="delete this part" 
			  class="btn btn-outline-danger btn-sm">Delete <big>&#128465;</big>
		  	</a>
		</span>
		<br>

		<span class="for-existing-items text-success">Choose Editor or
			<a href="javascript:void(0);" onclick="removeNewOnSongRow(this);" title="cancel">&#10007; cancel</a>
		</span>
		<br>
		@if ( isset($onsong) && $onsong->song_part->code!='m')
			<button title="Advanced OnSong Editor" type="button" class="btn btn-outline-primary btn-sm for-existing-items select-adv-onsong-editor" 
				onclick="showAdvOnSongEditor(this)">OnSong</button>
		@endif

		<button title="Plain OnSong-Text Editor" type="button" class="btn btn-outline-primary btn-sm for-existing-items" 
			onclick="showPlaintextEditor($(this).parent())">Plain text</button>

		<button title="Chords-Over-Lyrics Editor" type="button" class="btn btn-outline-primary btn-sm for-existing-items" 
			onclick="showChOLyEditor(this)">Chords-Over-Lyrics</button>
	</div>



	{{-- OnSong Editor 
	--}}
	<div id="advanced-editor-{{ $newOnsongRow ? '0' : $onsong->id }}" class="advanced-editor rounded bg-info text-white lh-2 px-1"></div>


	{{-- Read-only area to show chords-over-lyrics and action buttons
	--}}
	<div class="white-space-pre-wrap lh-1 write-onsong-text{{ $newOnsongRow || $onsong->song_part->code!='m' ? ' show-onsong-text' : '' }}{{ $newOnsongRow ? ' hidden' : '' }}" 
		 title="Click to edit" 
		 onclick="
				$(this).siblings('.toggle-onsong-buttons').click();
			">{{ $newOnsongRow ? '' : $onsong->text }}
	</div>


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

		<div class="card">
			<div class="card-block p-0">
		    	<span>Re-arrange the keys (chords) within the lyrics by dragging them left or right.</span>
		    	<button type="button" class="btn btn-sm float-right btn-success ml-1" onclick="submitEditedOnSong(  $(this))" >Save changes</button>
		    	<button type="button" class="btn btn-sm float-right btn-secondary"    onclick="closeAdvOnSongEditor($(this))" >Cancel</button>
			</div>
		</div>

	</div>


	<div class="text-editor-hints small hidden">

		<div class="card">
			<div class="card-block p-0">

				<span class="float-right ml-2 text-editor-save-cancel-buttons">
					<a href="javascript:void(0);" onclick="saveNewOnSongText(this);" title="save"   tabindex=2 
					  class="btn btn-sm btn-success float-right"><big>&#128427;</big><span class="hidden-sm-down text-white px-1">Save</span></a>
					<br>
					<a href="javascript:void(0);" onclick="removeNewOnSongRow(this);" title="cancel" tabindex=3 
					  class="btn btn-sm btn-secondary"><big>&#10007;</big><span class="hidden-sm-down"> Cancel </span></a>
				</span>

				<p class="card-text hints-for-plaintext-editor hidden mb-0">			
					The <strong>original OnSong format</strong> has the lyrics with chords interspersed and in square brackets, like this:<br>
					<i>"Amazing [D]Grace, how [G]sweet the [D]sound"</i><br>
					For <strong>instructions to musicians</strong>, you can add lines with text enclosed in round brackets like this:<br>
					"<i>(play twice) </i>". This will only be visible to musicians.<br>
				</p>

				<p class="card-text hints-for-chords-over-lyrics-editor hidden mb-0">			
					With the <strong>"chords-over-lyrics" format</strong>, you can edit the lyrics of a song more easily. However, you need to manually
					remove any excess dashes ('-') that might have been inserted in order to properly align the chords with the text.<br>
					Lines with text enclosed in brackets like this: "<i>(play twice)</i>" will only be seen by the musicians.
				</p>

				<p class="card-text hints-for-plaintext-editor hints-for-chords-over-lyrics-editor hidden mb-0">			
					<span class="text-danger">
						<strong>Note:</strong> Insert an empty line to force a new slide in the lyrics presentations.<br>
					</span>
					Add <strong>comments</strong> by inserting a '#' (sharp) sign at the start of the line. Those comments won't appear anywhere else.
				</p>


				<div class="card-text hints-for-onsong-metadata hidden">
					Use this part to provide information ("Metatags") about the song. Metatags are name/value pairs 
					where the name is on the left and the value on the right; separated by a colon.<br>
					<span class="float-left big">Example:</span>
					<pre class="float-left ml-1 mb-0">Capo: 3{{"\n"}}Key: D{{"\n"}}Tempo: 76</pre>
				</div>

				<small class="float-right">(For more information, see the 
					<a href="http://www.onsongapp.com/docs/features/formats/onsong/metadata/" target="new" class="text-info">
					OnSong manual on formats <i class="fa fa-external-link"></i></a>)</small>
			</div>
		</div>
	</div>




</div>
