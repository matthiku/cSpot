

<table class="table table-striped table-bordered table-sm" id="onsong-parts" 
		data-song-id="{{ isset($song) ? $song->id : '0' }}" data-update-onsong-url="{{ route('updateonsongparts') }}">

	<thead style="display: none;">
		<tr>
			<th class="text-xs-center" style="width: 80px;">Part</th>
			<th class="text-xs-center small hidden-md-down" style="width: 40px;">Code</th>
			<th>Text and Chords</th>
			<th style="width: 80px;"></th>
		</tr>
	</thead>


	<tfoot>
		<tr class="bg-faded link" onclick="insertNewOnSongRow();">
			<th colspan=3 id="insertNewOnSongRow-link" class="text-xs-center">
				<a href="#tbl-bottom"></a>
				<i class="fa fa-plus"></i> Add new Part
			</th>
		</tr>

		<tr style="display: none;" class="show-onsong-format-hint text-xs-center ">
			<td colspan=3 class="small text-success">
				You can insert "chords over lyrics" or OnSong-formatted chords and lyrics (lyrics with chords in square brackets).<br>
				For more information, see the 
				<a href="http://www.onsongapp.com/docs/features/formats/onsong/chords/" target="new" class="text-info">
					OnSong manual on formats</a><br>
				<div class="text-danger">
					<strong>Note:</strong> Blank lines will force a new slide in lyrics presentations but will be ignored when showing the chords.
				</div>
			</td>
		</tr>

	</tfoot>



	<tbody>


        @if ( isset($song) )
			@foreach ($song->onsongs as $onsong)
				<tr id="tbl-row-{{ $onsong->id }}" {{ $onsong->song_part->code=='m' ? 'class=onsong-meta-data' : '' }}
					data-onsong-id="{{ $onsong->id }}" data-part-id="{{ $onsong->song_part_id }}">

					<th {{ $onsong->song_part->code!='m' ? 'class=text-xs-center' : 'class=text-xs-right colspan=2' }}>
						{{ $onsong->song_part->code!='m' ? $onsong->song_part->name : 'Notes:' }}
						<span class="hidden-lg-up"><br>{{ $onsong->song_part->code!='m' ? '('.$onsong->song_part->code.')' : '' }}</span>
					</th>
					
					@if ($onsong->song_part->code!='m')
						<th class="text-xs-center hidden-md-down">{{ $onsong->song_part->code!='m' ? $onsong->song_part->code : '' }}</th>
					@endif

					<td class="cell-part-text">
						<div class="white-space-pre-wrap write-onsong-text{{ $onsong->song_part->code!='m' ? ' show-onsong-text' : '' }}" 
							onclick="editOnSongText(this);" title="Click to edit">{{ $onsong->text }}</div>
						<textarea style="width: 100%; display: none; font-size: small;" tabindex=1 onkeyup="calculateTextAreaHeight(this);">{{ $onsong->text }}</textarea>
						<div class="error-msg" style="display: none;">Enter text here.</div>
					</td>

					<td class="cell-part-action">
						<a href="javascript:void(0);" onclick="editOnSongText(this);"  title="edit"   class="for-existing-items big"><i class="fa fa-edit"></i></a>
						@if ($onsong->song_part->code!='m')
							<br><button title="Advanced OnSong Editor" type="button" class="btn btn-primary btn-sm for-existing-items" 
									data-toggle="modal" data-target="#advOnSongEditor">&#9997;</button>
						@endif
						<br>
						<a href="javascript:void(0);" onclick="deleteOnSongText(this);" title="delete" class="for-existing-items text-danger"><i class="fa fa-trash"></i></a>
						<a href="javascript:void(0);" onclick="saveNewOnSongText(this);" style="display: none;" title="save"   tabindex=2 class="for-new-items big">&#128427;</a>
						<br>
						<a href="javascript:void(0);" onclick="removeNewOnSongRow(this);" style="display: none;" title="cancel" tabindex=3 class="for-new-items">&#10007;</a>
					</td>

				</tr>
			@endforeach
			<script>
				// move the metadata row to the top of the table rows
				row = $(".onsong-meta-data");
				row.insertBefore(row.prevAll().last());
			</script>
		@endif


		{{-- row to enter new song parts - invisible at first
		--}}
		<tr style="display: none;" id="new-onsong-row">
		
			<th class="cell-part-name text-xs-center">
				<select class="new-onsong-field" tabindex=1>
					<option value="">Select ...</option>
					@foreach ($songParts as $part)
						<option value="{{ $part->id }}">{{ $part->name }}</option>
					@endforeach
				</select>
				<div class="error-msg" style="display: none;">Select a name!</div>
				<br>
				<a href="{{ url('admin/song_parts') }}" target="new" class="small">edit list <i class="fa fa-external-link"></i></a>
			</th>

			<th class="cell-part-code hidden-md-down"></th>

			<td class="cell-part-text">
				<div class="show-onsong-text write-onsong-text white-space-pre-wrap" style="display: none;"></div>
				<textarea class="new-onsong-field" style="width: 100%;" tabindex=2 onkeyup="calculateTextAreaHeight(this);"></textarea>
				<div class="error-msg" style="display: none;">Enter text here.</div>
			</td>

			<td class="cell-part-action">
				<a href="javascript:void(0);" onclick="editOnSongText(this);"  style="display: none;" title="edit"   class="for-existing-items big"><i class="fa fa-edit"></i></a>
				<a href="javascript:void(0);" onclick="deleteOnSongText(this);" style="display: none;" title="delete" class="for-existing-items text-danger float-xs-right"><i class="fa fa-trash"></i></a>
				<a href="javascript:void(0);" onclick="saveNewOnSongText(this);" title="save" tabindex=3 class="for-new-items big">&#128427;</a><br>
				<a href="javascript:void(0);" onclick="removeNewOnSongRow(this);" title="cancel" tabindex=4 class="for-new-items newrow-cancel-button">&#10007;</a>
			</td>
		</tr>


	</tbody>


</table>

<a id="tbl-bottom"></a>
