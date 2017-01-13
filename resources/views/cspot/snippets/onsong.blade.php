
{{-- 
	code to show the OnSong parts of a song
	and to provide the necessary tools to modify same (add, edit, delete) 
--}}





{{-- modal to select the proper part code 
--}}
<div class="modal fade" id="selectSongPartCodeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  	<div class="modal-dialog" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title">Select Song-Part Name</h5>
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