
{{-- 
	code to show the OnSong parts of a song
	and to provide the necessary tools to modify same (add, edit, delete) 
--}}





{{-- modal to select the proper part code 
--}}
<div class="modal" id="selectSongPartCodeModal" tabindex="-1" role="dialog" aria-labelledby="selectSongPartCodeModalLabel" aria-hidden="true">
  	<div class="modal-dialog" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title" id="selectSongPartCodeModalLabel">Select Song-Part Name</h5>
        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          			<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

	      	<div class="modal-body">
				<select id="new-onsong-part-selection" tabindex=1>
					@foreach ($songParts as $part)
						<option data-code="{{ $part->code }}" value="{{ $part->id }}">{{ $part->name }}</option>
					@endforeach
				</select>
				<div id="part-selection-error-msg" class="hidden">Select a part name!</div>
				<br>
				<a href="{{ url('admin/song_parts') }}" target="new" class="small">edit list <i class="fa fa-external-link"></i></a>
	      	</div>

	      	<div class="modal-footer">
	        	<button type="button" onclick="insertSelectedPartCode();" class="btn btn-primary">Select</button>
	        	<button type="button" onclick="removeNewOnSongRow($('.table-success')[0]);" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
	      	</div>

	    </div>
  	</div>
</div>



{{-- Modal to show Editor Help 
--}}
<div class="modal fade" id="showOnSongEditorHelp" tabindex="-1" role="dialog" aria-labelledby="OnSongEditorHelpLabel" aria-hidden="true">
  	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">
      		<div class="modal-header">
        		<h5 class="modal-title" id="OnSongEditorHelpLabel">OnSong Editor Help</h5>
        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          			<span aria-hidden="true">&times;</span>
        		</button>
      		</div>
      		<div class="modal-body small">

                When editing OnSong data, you can choose between 3 different editors: 
                <ul>
                    <li><strong>OnSong editor</strong> - Drag just the chords to the left or right, without changing the lyrics</li>
                    <li><strong>Plain Text editor</strong> - Edit the lyrics and chords data in the original OnSong format</li>
                    <li><strong>Chords-over-Lyrics editor</strong> - this is helpful for editing just the lyrics.</li>
                </ul>

				<p class="">
					The <strong>original OnSong format</strong> has the lyrics with chords interspersed and
					in square brackets, like this:<br><i>"Amazing [D]Grace, how [G]sweet the [D]sound"</i><br>
					Use the "Plain Text" editor to modify the data in this format. 
				</p>


				<p class="">
					With the <strong>"chords-over-lyrics" format</strong>, you can sometimes edit the lyrics or chords of a song more easily. However, you need 
					to make sure that the chords remain in the right place above the lyrics and you need to manually
					remove any excess dashes ('-') that might have been inserted in order to properly align the chords with the text.<br>
				</p>


				<h6>Formatting and Instructions for Musicians or Singers</h6>
				Add certain elements in order to structure and prepare the presentation of the lyrics and chords for musicians or singers.


				<ul>
					<li class="mb-1">Insert an <red>empty line</red> to <strong>force a new slide</strong> in the lyrics presentations.</li>

					<li class="mb-1">For <strong>instructions to musicians</strong>, you can add lines with text enclosed in round brackets like this:<br>
						"<red>(play twice) </red>". This will only be visible to musicians.</li>

					<li class="mb-1">Text in curly braces "{}" will be shown only in the lyrics presentation as <strong>instructions for the singers</strong>,<br> 
						like "<red>{women}</red>" - for only the women to sing that part of the song.</li>

					<li class="mb-1">In order to provide for songs where 2 groups of singers sing different lyrics at the same time, you have to create 
						a <strong>second region</strong> with lyrics by inserting a line containing this code: "<red>[region 2]</red>".</li>

					<li class="mb-1">Add <strong>comments</strong> by inserting a '<red>#</red>' (sharp) sign at the start of the line. Those won't appear in the presentations.</li>
				</ul>

				<small class="float-left">(For more information, see the 
					<a href="http://www.onsongapp.com/docs/features/formats/onsong/metadata/" target="new" class="text-info">
					OnSong manual on formats <i class="fa fa-external-link"></i></a>)</small>

			</div>

  			<div class="modal-footer">
        		<button type="button" class="btn btn-secondary" data-dismiss="modal">OK, thanks!</button>
      		</div>
    	</div>
  	</div>
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
			<div class="onsong-row {{ $onsong->song_part->code=='m' ? ' onsong-meta-data bg-faded' : '' }}"
			 	 id="tbl-row-{{ $onsong->id }}" 
				 data-onsong-id="{{ $onsong->id }}" data-part-id="{{ $onsong->song_part_id }}">

				{{-- show the part name and code above the onsong data
				--}}
				<div class="bg-info pl-2 rounded-top cell-part-name">

					{{ $onsong->song_part->code!='m' ? $onsong->song_part->name : '' }}
					<span>{{ $onsong->song_part->code!='m' ? '('.$onsong->song_part->code.')' : '' }}</span>

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
	<div class="onsong-row hidden" id="new-onsong-row">

		{{-- placeholder to show the part name and code above the onsong data
		--}}
		<div class="bg-info pl-2 rounded-top cell-part-name">
			Select song-part name and enter new lyrics/chords or other text:
		</div>

		@include('cspot.snippets.onsong_action', ['newOnsongRow' => true])

	</div>


</div>



{{-- Row with button to add new song part 
--}}
<div class="bg-inverse rounded insertNewOnSongRow-link">

	@if (Auth::user()->isEditor())
		<span onclick="insertNewOnSongRow();" class="btn btn-sm btn-success link"><i class="fa fa-plus"></i> Add new Part</span>
	@endif

	<span class="small float-right">
		<a href="http://www.logue.net/xp/" target="new"><span class="text-info">Tool for Transposing</span>
			<i class="fa fa-external-link"></i></a>
	</span>
</div>


<div class="row show-onsong-format-hint hidden rounded-bottom py-2 px-3 bg-faded text-right text-primary small">
	If the imported text contains tab-stopps, define how many spaces they should be replaced with:
	<input type="number" id="onsong-import-tab-size" value=4 style="width: 2rem" onchange="updateTabToSpacesRatio(this)">
</div>




<a id="tbl-bottom"></a>