
<table class="table table-striped table-bordered table-sm" id="onsong-parts" 
		data-song-id="{{$song->id }}" data-update-onsong-url="{{ route('updateonsongparts') }}">

	<thead>
		<tr>
			<th class="text-xs-center">Part</th>
			<th>Text and Chords</th>
			<th></th>
		</tr>
	</thead>


	<tfoot>
		<tr>
			<th colspan="3" class="text-xs-center"><a id="insertNewOnSongRow-link" href="#" onclick="insertNewOnSongRow();"><i class="fa fa-plus"></i> Add</a></th>
		</tr>
	</tfoot>



	<tbody>


		@foreach ($song->songTexts as $onsong)
			<tr data-onsong-id="{{ $onsong->id }}" data-part-id="{{ $onsong->song_part_id }}">

				<th class="text-xs-center">{{ $onsong->song_part->name }}</th>

				<td class="cell-two">
					<div class="show-onsong-text white-space-pre-wrap" onclick="editOnSongText(this);" title="Click to edit">{{ $onsong->text }}</div>
					<textarea style="width: 100%; display: none;">{{ $onsong->text }}</textarea>
					<div class="error-msg" style="display: none;">Enter text here.</div>
				</td>

				<td class="cell-three big">
					<a href="#" class="for-existing-items" onclick="editOnSongText(this);" title="edit"><i class="fa fa-edit"></i></a>
					<a href="#" class="for-existing-items float-xs-right" onclick="deleteOnSongText(this);" title="delete"><i class="fa fa-trash"></i></a>
					<a href="#" class="for-new-items" onclick="saveNewOnSongText(this);" style="display: none;" title="save">&#128427;</a>
					<a href="#" class="for-new-items float-xs-right" onclick="removeNewOnSongRow(this);" style="display: none;" title="cancel">&#10007;</a>
				</td>

			</tr>
		@endforeach



		{{-- firstly invisible row to enter new song parts 
		--}}
		<tr style="display: none;" id="new-onsong-row">
		
			<td class="cell-one text-xs-center">
				<select class="new-onsong-field">
					<option value="">Select ...</option>
					@foreach ($songParts as $part)
						<option value="{{ $part->id }}">{{ $part->name }}</option>
					@endforeach
				</select>
				<div class="error-msg" style="display: none;">Select a name!</div>
				<br>
				<a href="{{ url('admin/song_parts') }}" target="new" class="small">(edit list <i class="fa fa-external-link"></i>)</a>
			</td>

			<td class="cell-two">
				<div class="show-onsong-text white-space-pre-wrap" style="display: none;"></div>
				<textarea class="new-onsong-field" style="width: 100%;"></textarea>
				<div class="error-msg" style="display: none;">Enter text here.</div>
			</td>

			<td class="cell-three big">
				<a href="#" class="for-existing-items" onclick="editOnSongText(this);"   style="display: none;" title="edit"><i class="fa fa-edit"></i></a>
				<a href="#" class="for-existing-items float-xs-right" onclick="deleteOnSongText(this);" style="display: none;" title="delete"><i class="fa fa-trash"></i></a>
				<a href="#" class="for-new-items" onclick="saveNewOnSongText(this);"  title="save">&#128427;</a>
				<a href="#" class="for-new-items float-xs-right" onclick="removeNewOnSongRow(this);" title="cancel">&#10007;</a>
			</td>
		</tr>


	</tbody>


</table>

