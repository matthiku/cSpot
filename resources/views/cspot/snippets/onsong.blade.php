
{{--
	code to show the OnSong parts of a song
	and to provide the necessary tools to modify same (add, edit, delete)
--}}



@include('cspot.snippets.onsong_modals')




{{-- Show and edit the SEQUENCE
--}}
<div class="row bg-faded rounded mx-1 p-1">

	@if (Auth::user()->isEditor())
		<div class="col-12 pl-0">
			<h5 class="mb-1">
				Sequence: &nbsp;
				<span class="small link">
					<a onclick="$('.sequence-help-text').toggle();" class="small text-info">
						(<span class="sequence-help-text">show</span><span class="sequence-help-text hidden">hide</span> help)</a>
				</span>
				<span class="float-right">
			        @if (Auth::user()->isEditor())
			            <span id="sequence-song-id-{{ $song->id }}" onclick="$('.show-input-hint').show();"
			               class="editable-song-field lora link">{{ $song->sequence }}</span>
			        @endif
				</span>
			</h5>
		</div>

		<div id="song-parts-sequence" class="col-12{{ $song->onsongs->count() ? '' : ' hidden' }}">

			<span id="song-parts-drag-zone" class="pt-1">
				@php
					$missingCodes  = '';
					$existingCodes = '';
					$partCodeMissing = false;
				@endphp
				@foreach ($song->onsongs as $onsong)
					@php $partCode = $onsong->song_part->code; @endphp
					@if ($partCode != 'm')
						<span class="p-1 rounded edit-chords partcodes-draggable bg-warning text-white mr-1"
							id="partcodes-draggable-{{ $partCode }}">{{ $partCode }}</span>
						@php $existingCodes .= $partCode . ', '; @endphp
					@endif
				@endforeach
			</span>

			<span id="wastebin-or-moving-zone">
				<span id="sequence-drop-zone" class="mx-1 bg-danger text-white rounded p-1">
					@if ($song->sequence)
						@foreach (explode(',', $song->sequence) as $seq)
							<span class="p-1 rounded edit-chords item bg-success text-white mr-1" id="partcodes-sequence-{{ $seq }}">{{ $seq }}</span>
							@php
								if (strpos($existingCodes, $seq) === false) {
									$partCodeMissing = true;
									if (strpos($missingCodes, $seq) === false)
										$missingCodes .= $seq . ', ';
								}
							@endphp
						@endforeach
					@endif
				</span>

				<span id="song-parts-wastebin-zone" class="btn btn-sm bg-faded text-white align-top"
					title="drag codes from the red drop-zone into the waste bin to remove them from the sequence">
					<big>&#128465;</big> Bin</span>

				<span id="clear-sequence-btn" class="btn btn-sm btn-outline-danger link align-top" onclick="clearSequenceArea(this)"
					title="clear the whole the sequence (you still have to save this!)">
					<big>&#128497;</big> Clear Seq.</span>

				<span id="submit-sequence-button" class="btn btn-sm bg-success text-white align-text-top link hidden ml-2" onclick="submitChangedSequence();"
				   		title="submit new or changed sequence">
				   <big>&#128427; </big> Confirm!</span>
			</span>

			<span id="create-default-seq-button" class="p-1 ml-2{{ $song->sequence ? ' hidden' : '' }}">
				<span class="mx-1 btn btn-sm btn-secondary link" onclick="createDefaultSequence();"
				   		title="auto-create the default sequence from the existing song parts (Won't work if song contains irregular parts!)">
				   create default seq.</span>
			</span>

		</div>

		@if ($song->onsongs->count())
			<div class="small col-12 mt-2 px-0{{ $partCodeMissing ? '' : ' hidden'}} missing-parts-help-text">
				<span class="text-danger big">Warning! </span>This sequence contains code(s)<span
					  class="bg-success mx-1 px-1 rounded text-white">{{ substr($missingCodes, 0, strlen($missingCodes)-2) }}</span>
					  for which the corresponding song parts (below) are missing!
			</div>
		@else
			<div class="small col-12 mt-2 px-0 hidden missing-parts-help-text">
				<span class="text-danger big">Warning! </span>This sequence contains code(s)<span
					  class="bg-success mx-1 px-1 rounded text-white show-missing-codes"></span>
					  for which the corresponding song parts (below) are missing!
			</div>
		@endif
	@endif


	<div class="small col-12 mt-2 px-0{{ $song->onsongs->count() ? ' hidden' : '' }} no-onsong-sequence-help-text">
		Start adding OnSong parts in order to be able to create the sequence!
	</div>

	<div class="small col-12 mt-2 px-0 hidden sequence-help-text">
		To <span class="text-primary">create/modify</span> the sequence, drag the
			<span class="bg-warning text-white rounded px-1">part</span> <span class="bg-warning text-white rounded px-1">codes</span> and drop them into the
			<span class="bg-danger text-white rounded px-1">red&nbsp;zone (sequence)</span>. Then move them to the desired place.
		<br>
		To <span class="text-primary">remove</span> a part from the sequence, drag it from the
			<span class="bg-danger text-white rounded px-1">sequence</span> into the
			<span class="bg-inverse text-white rounded px-1">bin</span>. To remove all, click on the "Clear Seq" button.
		<br>
		<strong>Note:</strong> You can't delete a song part from the list below while it's still listed in the
			<span class="bg-danger text-white rounded px-1">sequence</span> above!
	</div>

</div>



<script>
	// function to auto-size the drop-zone if there are more than 6 items
	function adaptMinWidthOfDropZone(elem) {
		if (!elem) return;
		var dropZone = $(elem);
		var seqLen = dropZone.children().length;
		if (seqLen > 6) {
			var dropItemWidth = $(dropZone.children()[0]).css('width').split('px')[0] || 28;
			dropZone.css('min-width', 1.2 * dropItemWidth * (seqLen+1)  );
		}
		$('#submit-sequence-button').show();
	}


	// remove all part codes from the sequence area
	function clearSequenceArea(that) {
		$('#sequence-drop-zone').html('');
		$(that).hide();
		$('#create-default-seq-button').show();
		$('#submit-sequence-button').show();
		$('.missing-parts-help-text').hide();
	}


	// auto-create default sequence (works only for simple songs without bridge)
	function createDefaultSequence() {
		// get part list from drag zone
		var partlist = $('#song-parts-drag-zone').text().trim().toLowerCase().split(/\s+/);

	    var chorus = partlist.indexOf('c') >= 0;
	    var insChorus = 1; // indicates verse number afer which we have to insert a chorus

	    // check if we have an Intro - will be inserted first
	    if (partlist.indexOf('i') >= 0)
	    	dragPartIntoSequence('i');

	    // loop through all possible verse numbers (hopefully, a song never has more than 8 verses....)
		for (var i = 1; i < 9; i++) {
			if ( partlist.indexOf(i.toString()) >= 0 ) {
				dragPartIntoSequence(i.toString());
				if (chorus)
					dragPartIntoSequence('c');
			}
		}
		// check if we have an ending
	    if (partlist.indexOf('e') >= 0)
	    	dragPartIntoSequence('e');

	    // hide the button who called this function
		$('#create-default-seq-button').hide();
		$('#clear-sequence-btn').show();

		adaptMinWidthOfDropZone($('#sequence-drop-zone'));
	}
	function dragPartIntoSequence(code) {
		var item = '<span class="p-1 rounded edit-chords item bg-success text-white mr-1" id="partcodes-sequence-'+code+'">'+code+'</span>';
		$('#sequence-drop-zone').append(item);
		makeSequenceItemsDraggable();
	}

	// write the new or updated sequence into the Editable field and trigger the update
	function submitChangedSequence() {
		// get part list from drag zone
		var seq = getPartsSequenceListFromDragZone();

		// write the new sequence into the editable input field
		$('.editable-song-field').text(seq);
		$('.editable-song-field').removeClass('invisible');
		// trigger the 'click' event in order to start the editable feature
		$('.editable-song-field').click();
		// click on the 'Submit' button
		$($('.editable-song-field > form > button')[0]).click();
		// hide the 'Save' button again
		$('#submit-sequence-button').hide();
		$('#clear-sequence-btn').show();
	}

	function getPartsSequenceListFromDragZone() {
		var seq = '';
		var parts = $('#sequence-drop-zone').children();
		parts.each(function(i) {
			seq += $(parts[i]).text().trim() + ',';
		})
		if (seq=='')
			seq ='_';
		else
			// remove trailing comma
			seq = seq.substr(0,seq.length-1);
		return seq;
	}

	// make the part codes dragg-able (must be a callable function as its needed after adding a new part)
	function makePartCodesDraggable() {
		$( ".partcodes-draggable" ).draggable({
		  	containment: '#sequence-drop-zone',
		  	helper: 'clone',
		  	grid: [ 10, 5 ]
		});
	}
	makePartCodesDraggable();

	// make the drop-zone drop-able
	$("#sequence-drop-zone" ).droppable({
	  	drop:
		  	function(event, ui) {
		  		// hide auto-creation button now but make sure Clear btn is visible
		  		$('#create-default-seq-button').hide();
				$('#clear-sequence-btn').show();

		  		// no cloning for items already in this zone
		  		if (ui.draggable[0].classList.contains('item'))
		  			return;

		  		// clone the dropped part and make it draggable again
		  		$(this).append($(ui.draggable).clone());
	            $("#sequence-drop-zone .partcodes-draggable").addClass("item");
	            $("#sequence-drop-zone .item").removeClass("ui-draggable partcodes-draggable bg-warning");
	            $("#sequence-drop-zone .item").addClass("bg-success");
				makeSequenceItemsDraggable();
	            adaptMinWidthOfDropZone(this);
		  	},
	  	activate:
	  		function(event, ui) {
	  			that = this;
	  			$(that).removeClass('bg-danger').addClass('bg-primary');
	  			setTimeout( function() {
	  				$(that).addClass('bg-danger').removeClass('bg-primary');
		  			setTimeout( function() {
		  				$(that).addClass('bg-danger').removeClass('bg-primary');
		  			}, 500);
	  			}, 500);
	  		},
		});


	// the existing sequence items
	function makeSequenceItemsDraggable() {
		$("#sequence-drop-zone").sortable({
			axis : 'x',
		  	forcePlaceholderSize: true,
		  	opacity: 0.8,
	        placeholder: "ui-state-highlight",
	        start: function(event, ui) {
	            ui.placeholder.html('__');
	        },
	        update: function(event, ui) {
	        	$('#submit-sequence-button').show();
	        },
		});
	}
	makeSequenceItemsDraggable();

	// the waste bin ....
	$( "#song-parts-wastebin-zone" ).droppable({
	  	drop:
		  	function(event, ui) {
		  		// only allow items from the drop-zone to be removed
		  		if (ui.draggable[0].classList.contains('item'))
		  			$(ui.draggable).remove();

		  		// adapt the minimum width of the dropZone
	            adaptMinWidthOfDropZone($('#sequence-drop-zone'));

	            // show auto-creation button again if drop-zone is empty
	            // (we have to delay this as the dragging is not yet fully finished!)
		  		setTimeout( function() {
	            	if ( ! $('#sequence-drop-zone').children().length )
		  				$('#create-default-seq-button').show();
		  				$('#clear-sequence-btn').hide();
		  		}, 900);
		  	},
	  	activate:
	  		function(event, ui) {
	  			wb = this;
	  			$(wb).removeClass('bg-inverse').addClass('bg-primary');
	  			setTimeout( function() {
	  				$(wb).addClass('bg-inverse').removeClass('bg-primary');
		  			setTimeout( function() {
		  				$(wb).addClass('bg-inverse').removeClass('bg-primary');
		  			}, 500);
	  			}, 500);
	  		},
		});

	// highlight the waste bin once the cursor moves over the drag zone
	$("#sequence-drop-zone" ).on('mouseover', function() {
        $('#song-parts-wastebin-zone').removeClass('bg-faded');
        $('#song-parts-wastebin-zone').addClass('bg-inverse');
	});

</script>






<div class="show-collapse-expand-parts-link {{ $song->onsongs->count() ? '' : 'hidden'}} small text-right mt-1 mr-2">
	<a href="#" class="link text-info" onclick="
			$('.cell-part-text').toggleClass('show');
			$('.collapse-button-text').toggle();
			document.body.scrollTop = document.documentElement.scrollTop = 0;">
		<span class="collapse-button-text">collapse</span>
		<span class="collapse-button-text hidden">expand</span>
		all parts
	</a>
</div>




{{-- DIV element to show the actual OnSong data - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
--}}
<div id="onsong-parts"

	 class="container-fluid px-0 px-sm-1"

	 data-song-id="{{ isset($song) ? $song->id : '0' }}"
	 data-update-onsong-url="{{ route('updateonsongparts') }}">




	{{-- insert each OnSong part as a div into the DOM
	--}}
    @if ( isset($song) )

		@foreach ($song->onsongs->sortBy('song_part.sequence') as $onsong)
			<div class="onsong-row rounded-bottom mb-2 {{ $onsong->song_part->code=='m' ? ' onsong-meta-data bg-faded' : '' }}"
			 	 id="tbl-row-{{ $onsong->id }}" role="tablist" aria-multiselectable="true"
				 data-onsong-id="{{ $onsong->id }}" data-part-id="{{ $onsong->song_part_id }}" data-seq-no="{{ $onsong->song_part->sequence }}">

				{{-- show the part name and code above the onsong data
				--}}
				<div class="cell-part-name bg-info pl-2 py-1 link rounded-top" role="tab" data-part-code="{{ $onsong->song_part->code }}"
        				data-toggle="collapse" data-parent="#onsong-parts" href="#collapse-{{ $onsong->song_part_id }}"
        									  aria-expanded="true" aria-controls="collapse-{{ $onsong->song_part_id }}"
						id="heading-{{ $onsong->song_part_id }}">
					<h6 class="mb-0">
        				<a onclick="removeNewOnSongRow($(this).parents('.onsong-row'));">
							<span class="float-right mr-1">&#9660;</span>
							<span class="float-right">&#9664;</span>
							<span class="song-part-name">
								{{  $onsong->song_part->name }}
								@if ($onsong->song_part->code != 'm')
									<span class="text-white">({{ $onsong->song_part->code }})</span>
								@endif
							</span>
			        	</a>
      				</h6>
				</div>

				{{-- actual data and editors --}}
				@include('cspot.snippets.onsong_action', ['newOnsongRow' => false])


			</div>
		@endforeach

		<script>
			// make sure the Meta-Data row is always at the top of the other OnSong parts
			row = $(".onsong-meta-data");
			if (row.length)
				row.insertBefore(row.prevAll().last());
		</script>

	@endif





	{{-- row to enter NEW song parts - invisible at first
	--}}
	<div class="onsong-row rounded-bottom mb-2 hidden" id="new-onsong-row" role="tablist" aria-multiselectable="true">

		{{-- show the part name and code above the onsong data
		--}}
		<div class="cell-part-name bg-info pl-2 py-1 link rounded-top" role="tab" data-part-code="" id="heading-0"
				data-toggle="collapse" data-parent="#onsong-parts" href="#collapse-0"
										 aria-expanded="true" aria-controls="collapse-0">
			<h6 class="mb-0">
				<a onclick="removeNewOnSongRow($(this).parents('.onsong-row'));">
					<span class="float-right mr-1">&#9660;</span>
					<span class="float-right">&#9664;</span>
					<span class="song-part-name">
						Select song-part name and enter new lyrics/chords or other text:
					</span>
	        	</a>
			</h6>
		</div>

		@include('cspot.snippets.onsong_action', ['newOnsongRow' => true])

	</div>


</div>





{{-- Row with button to add new song part
--}}
<div class="insertNewOnSongRow-link bg-inverse rounded pr-2" style="padding: 2px;">

	@if (Auth::user()->isEditor())
		<span onclick="insertNewOnSongRow();"
			title="Manually add (or paste) a singe OnSong part to this song"
			class="btn btn-sm btn-success link onsong-add-button"><i class="fa fa-plus"></i> Add new Part</span>

		@if (isset($song) && ! $song->onsongs->count())
			<span onclick="
					$('.show-onsong-paste-hint').hide();
					$('.show-onsong-upload-hint').toggle();
					$('#onsong-submit-method').val('POST');"
				title="Import an existing OnSong or ChordPro file from your computer"
				class="btn btn-sm btn-info link ml-2 onsong-import-buttons">&#9088; Import OnSong File</span>

			<span onclick="
					$('.show-onsong-upload-hint').hide();
					$('.show-onsong-paste-hint').toggle();"
				title="Past a complete song with chords (of any format) and convert it into OnSong parts"
				class="btn btn-sm btn-info link ml-2 onsong-import-buttons">&#9088; Paste Whole Song</span>

			@if ($song->chords)
				<span onclick="
						$('.onsong-import-areas').hide();
						convertChordsToOnSongParts();"
					title="Use the existing chords data of this song and convert it into OnSong parts"
					class="btn btn-sm btn-info link ml-2 onsong-import-buttons">&#9088; Convert Existing Chords</span>
			@endif
		@endif
	@endif

	@if ( Auth::user()->isEditor())  &&  isset($song) && $song->onsongs->count() )
		<span class="small float-right mt-1">
			<a href="http://www.logue.net/xp/" target="new"><span class="text-info">Transposing Tool</span>
				<i class="fa fa-external-link text-white"></i></a>
		</span>

		<span onclick="
				$('.onsong-import-areas').hide();
				$('.show-onsong-transpose-hint').toggle();
				location.href = '#tbl-bottom';
				$('#transpose-oldkey').focus();"
			title="Transpose this song"
			class="btn btn-sm btn-info link mx-2 onsong-import-buttons">&#9088; Transpose Song</span>
	@endif
</div>



{{-- provide UPLOAD facility for a full song
--}}
<div class="show-onsong-upload-hint onsong-import-areas hidden rounded-bottom py-2 px-3 bg-faded text-primary small">
	Select (or drop here) a <strong>valid OnSong file</strong> to be processed for this song:
	<input id="fileupload" type="file" name="onsongfile" data-url="{{ route('uploadonsongfiles', isset($song) ? $song->id : '0') }}">
	<input id="onsong-submit-method" type="hidden" name="_method" value="PUT">
	<div id="progress">
	    <div class="bar" style="width: 0%;"></div>
	</div>
</div>



{{-- provide textarea to PASTE full song
--}}
<div class="show-onsong-paste-hint onsong-import-areas hidden rounded-bottom py-2 px-3 bg-faded text-center text-primary small">

	Copy a complete <strong>OnSong-formatted</strong> or <strong>Chords-over-Lyrics</strong>-formatted song into the input area below:

	<textarea id="onsong-paste-song" class="fully-width rounded"
		onkeyup="calculateTextAreaHeight(this);" style="min-height: 10rem;"></textarea>

	Make sure everything is correct then:
	<button type="button" class="btn btn-primary" onclick="submitPastedOnSongText();">Submit</button>
</div>



{{-- provide selection of from-key and to-key for TRANSPOSING
--}}
<div class="show-onsong-transpose-hint hidden rounded-bottom py-2 px-3 bg-faded text-right text-primary small">
	@include ('cspot.snippets.onsong_transpose')
</div>




<div class="show-onsong-format-hint hidden rounded-bottom py-2 px-3 bg-faded text-right text-primary small">
	Select the appropriate editor! (See help under "Onsong Help"-Button!)<br><br>
	For importing: if the imported text contains tab-stopps, define how many spaces they should be replaced with:
	<input type="number" id="onsong-import-tab-size" value=4 style="width: 2rem" onchange="updateTabToSpacesRatio(this);">
</div>




<a id="tbl-bottom"></a>


{{-- file-upload function
	see: https://github.com/blueimp/jQuery-File-Upload/wiki/Basic-plugin
--}}
@if (isset($song) && ! $song->onsongs->count())
	<script>
		$(function () {
		    $('#fileupload').fileupload({
		    	dropZone: $('.show-onsong-upload-hint'),
		    	type: 'POST',
		    	dataType: 'json',
			    /* show progress */
			    progressall: function (e, data) {
			        var progress = parseInt(data.loaded / data.total * 100, 10);
			        $('#progress .bar').css(
			            'width',
			            progress + '%'
			        );
			    },
			    done: function (e, data) {
			    	if (data.textStatus=='success') {
			    		var data = JSON.parse(data.result.data);
			    		processOnSongFile(data);
			    	}
			    	else
			    		console.log(data);
			    },
		    });
		});
	</script>
@endif
