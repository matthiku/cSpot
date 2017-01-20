

{{-- 
	table cell to show the actual OnSong data, provide the editors and all relevant action buttons 
--}}


<div class="cell-part-text collapse show" id="collapse-{{ $newOnsongRow ? '0' : $onsong->song_part_id }}" role="tabpanel" aria-labelledby="heading-{{ $newOnsongRow ? '0' : $onsong->song_part_id }}">

	@if (Auth::user()->isEditor())
		<span class="toggle-onsong-buttons float-right btn btn-sm link" title="Show Edit Buttons" 
			onclick="toggleOnSongEditButtons($(this).parents('.onsong-row'));">&#9997;</span>
	@endif




	{{-- show action buttons 
	--}}
	<div class="cell-part-action m-1 p-1 float-right small hidden">

		<span class="float-right for-existing-items">
			<a href="javascript:void(0);" onclick="deleteOnSongText($(this).parents('.onsong-row'));" title="delete this part" 
			  class="text-editor-delete-button btn btn-outline-danger btn-sm">Delete <big>&#128465;</big>
		  	</a>
		</span>
		<br>

		<span class="for-existing-items text-success">Choose Editor or
			<a href="javascript:void(0);" onclick="removeNewOnSongRow($(this).parents('.onsong-row'));" title="cancel" class="text-danger">&#10007; cancel</a>
		</span>

		<br>

		<!-- trigger help modal -->
		<button type="button" class="btn btn-sm btn-info for-existing-items mr-1" data-toggle="modal" data-target="#showOnSongEditorHelp">Help</button>

		@if ( $newOnsongRow || (isset($onsong) && $onsong->song_part->code!='m') )
			<button title="Advanced OnSong Editor" type="button" class="btn btn-outline-primary btn-sm link for-existing-items select-adv-onsong-editor" 
				onclick="showAdvOnSongEditor($(this).parents('.onsong-row'))">OnSong</button>
		@endif

		<button title="Plain OnSong-Text Editor" type="button" class="btn btn-outline-primary btn-sm link for-existing-items" 
			onclick="showPlaintextEditor($(this).parents('.onsong-row'))">Plain text</button>

		<button title="Chords-Over-Lyrics Editor" type="button" class="btn btn-outline-primary btn-sm link for-existing-items" 
			onclick="showChOLyEditor($(this).parents('.onsong-row'))">Chords-Over-Lyrics</button>
	</div>





	{{-- OnSong Editor 
	--}}
	<div id="advanced-editor-{{ $newOnsongRow ? '0' : $onsong->id }}" class="advanced-editor rounded bg-info text-white lh-2 px-1"></div>




	{{-- Read-only area to show chords-over-lyrics and action buttons
	--}}
	<div class="white-space-pre-wrap lh-1 link write-onsong-text{{ $newOnsongRow || $onsong->song_part->code!='m' ? ' show-onsong-text' : '' }}{{ $newOnsongRow ? ' hidden' : '' }}" 
		 title="Click to edit" style="min-height: 3rem;" 
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
		    	<button type="button" class="btn btn-sm float-right btn-success ml-1" onclick="submitEditedOnSong(  $(this).parents('.onsong-row'))" >Save changes</button>
		    	<button type="button" class="btn btn-sm float-right btn-secondary"    onclick="closeAdvOnSongEditor($(this).parents('.onsong-row'))" >Cancel</button>
			</div>
		</div>

	</div>




	<div class="text-editor-hints small hidden">

		<div class="card">
			<div class="card-block p-0 center">

				<span class="text-editor-save-cancel-buttons">
					<!-- help modal -->
					<button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#showOnSongEditorHelp">
	  					Help on Editor usage
					</button>

					<a href="javascript:void(0);" onclick="saveNewOnSongText($(this).parents('.onsong-row'));" title="Save your changes"   tabindex=2 
					  class="btn btn-sm btn-success"><big>&#128427;</big><span class="hidden-xs-down text-white px-1">Save</span></a>

					<a href="javascript:void(0);" onclick="removeNewOnSongRow($(this).parents('.onsong-row'));" title="cancel" tabindex=3 
					  class="btn btn-sm btn-secondary my-1"><big>&#10007;</big><span class="hidden-xs-down"> Cancel </span></a>

					<a href="javascript:void(0);" onclick="deleteOnSongText($(this).parents('.onsong-row'));" title="delete this part" tabindex=4 
					  class="text-editor-delete-button hidden btn btn-sm btn-danger"><big>&#128465;</big><span class="hidden-xs-down"> Delete </span></a>
				</span>


				<div class="card-text hints-for-onsong-metadata hidden">
					Use this part to provide information ("Metatags") about the song. Metatags are name/value pairs 
					where the name is on the left and the value on the right; separated by a colon.<br>
					<span class="float-left big">Example:</span>
					<pre class="float-left ml-1 mb-1">Capo: 3{{"\n"}}Key: D{{"\n"}}Tempo: 76</pre>
				</div>


			</div>
		</div>
	</div>




</div>
