
{{-- 
	code to show the OnSong parts of a song
	and to provide the necessary tools to modify same (add, edit, delete) 
--}}


@include('cspot.snippets.onsong_modals')



{{-- Show and edit the SEQUENCE 
--}}
<div class="row bg-faded rounded mx-1 p-1">

	<div class="col-12 pl-0">
		<h5 class="mb-1">
			Sequence: &nbsp;
			<span class="small link">
				<a href="#" onclick="$('.sequence-help-text').toggle();" class="small">
					(<span class="sequence-help-text">show</span><span class="sequence-help-text hidden">hide</span> help</a>)
			</span>
			<span class="float-right">
		        @if (Auth::user()->isEditor())
		            <span id="sequence-song-id-{{ $song->id }}" onclick="$('.show-input-hint').show();" 
		               class="editable-song-field lora link">{{ $song->sequence }}</span>
		        @endif
			</span>
		</h5>
	</div>

	<div id="song-parts-sequence" class="col-12">

		<span id="song-parts-drag-zone" class="pt-1">
			@foreach ($song->onsongs as $onsong)
				@if ($onsong->song_part->code!='m')
					<span class="p-1 rounded edit-chords partcodes-draggable bg-warning text-white mr-1" 
						id="partcodes-draggable-{{ $onsong->song_part->code }}">{{ $onsong->song_part->code }}</span>
				@endif
			@endforeach
		</span>
		
		<span id="wastebin-or-moving-zone">	
			<span id="sequence-drop-zone" class="mx-1 bg-danger text-white rounded p-1">
				@if ($song->sequence)
					@foreach (explode(',', $song->sequence) as $seq)
						<span class="p-1 rounded edit-chords item bg-success text-white mr-1" id="partcodes-sequence-{{ $seq }}">{{ $seq }}</span>
					@endforeach
				@endif
			</span>

			<span class="btn btn-sm bg-inverse text-white align-top" id="song-parts-wastebin-zone"
				title="drag codes from the drop-zone into the waste bin to remove them from the sequence">
				<big>&#128465;</big> Bin</span>

			<span class="btn btn-sm bg-success text-white align-text-top link hidden" id="submit-sequence-button" onclick="submitChangedSequence();" 
			   		title="submit new or changed sequence">
			   <big>&#128427; </big> Save</span>
		</span>

		@if (! $song->sequence)
			<span class="p-1" id="create-default-seq-button">
				<span class="mx-1 btn btn-sm btn-secondary link" onclick="createDefaultSequence();" 
				   		title="auto-create the default sequence from the existing song parts (Won't work if song contains irregular parts!)">
				   create default seq.</span>
			</span>
		@endif

	</div>


	<div class="small col-12 mt-2 px-0 hidden sequence-help-text">
		To <span class="text-primary">create/modify</span> the sequence, drag the 
			<span class="bg-warning text-white rounded px-1">part</span> <span class="bg-warning text-white rounded px-1">codes</span> into the 
			<span class="bg-danger text-white rounded px-1">red&nbsp;zone (sequence)</span>.
		<br>
		To <span class="text-primary">remove</span> a part from the sequence, drag it from the 
			<span class="bg-danger text-white rounded px-1">sequence</span> into the 
			<span class="bg-inverse text-white rounded px-1">bin</span>.
		<br>
		<strong>Note:</strong> You can't delete a song part from the list below while it's still listed in the
			<span class="bg-danger text-white rounded px-1">sequence</span> above!
	</div>

</div>



<script>
	// function to auto-size the drop-zone if there are more tha 6 items
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
		  		// hide auto-creation button now
		  		$('#create-default-seq-button').hide();

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
	  			$('#sequence-drop-zone').removeClass('bg-danger').addClass('bg-primary');
	  			setTimeout( function() {
	  				$('#sequence-drop-zone').addClass('bg-danger').removeClass('bg-primary');
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
	            if ( $('#sequence-drop-zone').children().length == 0 )
		  			$('#create-default-seq-button').show();
		  	},
		});
	
</script>






<div class="small text-right mt-1 mr-2">
	<a href="#" class="link" onclick="
			$('.cell-part-text').toggleClass('show');
			$('.collapse-button-text').toggle();">
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

		@foreach ($song->onsongs as $onsong)
			<div class="onsong-row rounded-bottom mb-2 {{ $onsong->song_part->code=='m' ? ' onsong-meta-data bg-faded' : '' }}"
			 	 id="tbl-row-{{ $onsong->id }}" role="tablist" aria-multiselectable="true"
				 data-onsong-id="{{ $onsong->id }}" data-part-id="{{ $onsong->song_part_id }}">

				{{-- show the part name and code above the onsong data
				--}}
				<div class="cell-part-name bg-info pl-2 rounded-top" role="tab" data-part-code="{{ $onsong->song_part->code }}" id="heading-{{ $onsong->song_part_id }}">
					<h5 class="mb-0">
        				<a data-toggle="collapse" data-parent="#onsong-parts" href="#collapse-{{ $onsong->song_part_id }}"
        										 aria-expanded="true" aria-controls="collapse-{{ $onsong->song_part_id }}"
        										 onclick="removeNewOnSongRow($(this).parents('.onsong-row'));">
							{{ $onsong->song_part->code!='m' ? $onsong->song_part->name : '' }}
							@if ($onsong->song_part->code!='m')
								<span class="text-white">({{ $onsong->song_part->code }})</span>
							@endif
			        	</a>
      				</h5>
				</div>

				{{-- actual data and editors --}}
				@include('cspot.snippets.onsong_action', ['newOnsongRow' => false])


			</div>
		@endforeach

		<script>
			// move the metadata row to the top of the table rows
			row = $(".onsong-meta-data");
			row.insertBefore(row.prevAll().last());
		</script>

	@endif





	{{-- row to enter NEW song parts - invisible at first
	--}}
	<div class="onsong-row rounded-bottom mb-2 hidden" id="new-onsong-row" role="tablist" aria-multiselectable="true">

		{{-- placeholder to show the part name and code above the onsong data
		--}}
		<div class="bg-info pl-2 rounded-top cell-part-name" role="tab" data-part-code="" id="heading-0">
			<h5 class="mb-0">
				<a data-toggle="collapse" data-parent="#onsong-parts" href="#collapse-0"
										 aria-expanded="true" aria-controls="collapse-0"
        								 onclick="removeNewOnSongRow($(this).parents('.onsong-row'));">
					Select song-part name and enter new lyrics/chords or other text:
	        	</a>
			</h5>
		</div>

		@include('cspot.snippets.onsong_action', ['newOnsongRow' => true])

	</div>


</div>



{{-- Row with button to add new song part 
--}}
<div class="insertNewOnSongRow-link bg-inverse rounded ">

	@if (Auth::user()->isEditor())
		<span onclick="insertNewOnSongRow();" class="btn btn-sm btn-success link"><i class="fa fa-plus"></i> Add new Part</span>
	@endif

	<span class="small float-right">
		<a href="http://www.logue.net/xp/" target="new"><span class="text-info">Tool for Transposing</span>
			<i class="fa fa-external-link"></i></a>
	</span>
</div>


<div class="show-onsong-format-hint hidden rounded-bottom py-2 px-3 bg-faded text-right text-primary small">
	If the imported text contains tab-stopps, define how many spaces they should be replaced with:
	<input type="number" id="onsong-import-tab-size" value=4 style="width: 2rem" onchange="updateTabToSpacesRatio(this)">
</div>




<a id="tbl-bottom"></a>