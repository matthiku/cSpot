
<table class="table table-striped table-bordered table-sm" id="onsong-parts" 
		data-song-id="{{ isset($song) ? $song->id : '0' }}" data-update-onsong-url="{{ route('updateonsongparts') }}">

	<thead>
		<tr>
			<th class="text-xs-center" style="width: 80px;">Part</th>
			<th class="text-xs-center small hidden-xs-down" style="width: 40px;">Code</th>
			<th>Text and Chords</th>
			<th style="width: 80px;"></th>
		</tr>
	</thead>


	<tfoot>
		<tr class="bg-faded link" onclick="insertNewOnSongRow();">
			<th colspan="4" id="insertNewOnSongRow-link" class="text-xs-center">
				<a href="#tbl-bottom"></a>
				<i class="fa fa-plus"></i> Add new Part
			</th>
		</tr>
		<tr style="display: none;"><td><a href="tbl-bottom"></a></td></tr>
	</tfoot>



	<tbody>


        @if ( isset($song) )
			@foreach ($song->onsongs as $onsong)
				<tr id="tbl-row-{{ $onsong->id }}" {{ $onsong->song_part->code=='m' ? 'class=onsong-meta-data' : '' }}
					data-onsong-id="{{ $onsong->id }}" data-part-id="{{ $onsong->song_part_id }}">

					<th class="text-xs-center">{{ $onsong->song_part->code!='m' ? $onsong->song_part->name : '(Notes)' }}</th>
					
					<th class="text-xs-center hidden-xs-down">{{ $onsong->song_part->code!='m' ?$onsong->song_part->code : '' }}</th>

					<td class="cell-part-text">
						<div class="white-space-pre-wrap write-onsong-text{{ $onsong->song_part->code!='m' ? ' show-onsong-text' : '' }}" 
							onclick="editOnSongText(this);" title="Click to edit">{{ $onsong->text }}</div>
						<textarea style="width: 100%; display: none; font-size: small;" tabindex=1 onkeyup="calculateTextAreaHeight(this);">{{ $onsong->text }}</textarea>
						<div class="error-msg" style="display: none;">Enter text here.</div>
					</td>

					<td class="cell-part-action big">
						<a href="javascript:void(0);" class="for-existing-items" onclick="editOnSongText(this);" title="edit"><i class="fa fa-edit"></i></a>
						<a href="javascript:void(0);" class="for-existing-items float-xs-right" onclick="deleteOnSongText(this);" title="delete"><i class="fa fa-trash"></i></a>
						<a href="javascript:void(0);" class="for-new-items" onclick="saveNewOnSongText(this);" style="display: none;" title="save" tabindex=2>&#128427;</a>
						<a href="javascript:void(0);" class="for-new-items float-xs-right" onclick="removeNewOnSongRow(this);" style="display: none;" title="cancel" tabindex=3>&#10007;</a>
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
				<a href="{{ url('admin/song_parts') }}" target="new" class="small">(edit list <i class="fa fa-external-link"></i>)</a>
			</th>

			<th class="cell-part-code"></th>

			<td class="cell-part-text">
				<div class="show-onsong-text write-onsong-text white-space-pre-wrap" style="display: none;"></div>
				<textarea class="new-onsong-field" style="width: 100%;" tabindex=2 onkeyup="calculateTextAreaHeight(this);"></textarea>
				<div class="error-msg" style="display: none;">Enter text here.</div>
			</td>

			<td class="cell-part-action big">
				<a href="javascript:void(0);" class="for-existing-items" onclick="editOnSongText(this);"   style="display: none;" title="edit"><i class="fa fa-edit"></i></a>
				<a href="javascript:void(0);" class="for-existing-items float-xs-right" onclick="deleteOnSongText(this);" style="display: none;" title="delete"><i class="fa fa-trash"></i></a>
				<a href="javascript:void(0);" class="for-new-items" onclick="saveNewOnSongText(this);"  title="save" tabindex=3>&#128427;</a>
				<a href="javascript:void(0);" class="for-new-items float-xs-right newrow-cancel-button" onclick="removeNewOnSongRow(this);" title="cancel" tabindex=4>&#10007;</a>
			</td>
		</tr>


	</tbody>


</table>

