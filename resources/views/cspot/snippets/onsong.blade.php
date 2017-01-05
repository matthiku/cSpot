



<table class="table table-striped table-sm" id="onsong-parts" 
		data-song-id="{{ isset($song) ? $song->id : '0' }}" data-update-onsong-url="{{ route('updateonsongparts') }}">


	<tfoot>
		<tr class="bg-faded link" id="insertNewOnSongRow-link">
			<th colspan=3 class="pl-2">
				<span onclick="insertNewOnSongRow();" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Add new Part</span>
				<span class="small float-xs-right">
					<a href="http://www.logue.net/xp/" target="new"><span class="text-info">Tool for Transposing</span>
						<i class="fa fa-external-link"></i></a>
				</span>
			</th>
		</tr>

		<tr class="show-onsong-format-hint text-xs-center hidden">
			<td colspan=3 class="small">
				<p class="card">
					If the imported text contains tab-stopps, define how many spaces they should be replaced with:
					<input type="number" id="onsong-import-tab-size" value=4 style="width: 2rem" onchange="updateTabToSpacesRatio(this)">
				</p>
			</td>
		</tr>

	</tfoot>



	<tbody>


        @if ( isset($song) )
			@foreach ($song->onsongs as $onsong)
				<tr id="tbl-row-{{ $onsong->id }}" {{ $onsong->song_part->code=='m' ? 'class=onsong-meta-data' : '' }}
					data-onsong-id="{{ $onsong->id }}" data-part-id="{{ $onsong->song_part_id }}">

					<th {{ $onsong->song_part->code!='m' ? 'class=text-xs-center' : 'class=text-xs-right' }} width="100">
						{{ $onsong->song_part->code!='m' ? $onsong->song_part->name : '' }}
						<span><br>{{ $onsong->song_part->code!='m' ? '('.$onsong->song_part->code.')' : '' }}</span>
					</th>


					<td class="cell-part-text">
						@include('cspot.snippets.onsong_action', ['newOnsongRow' => false])
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
		
			<th class="cell-part-name text-xs-center" width="100">
				<select class="new-onsong-field" tabindex=1>
					<option value="">Select ...</option>
					@foreach ($songParts as $part)
						<option value="{{ $part->id }}">{{ $part->name }}</option>
					@endforeach
				</select>
				<div class="error-msg hidden">Select a name!</div>
				<br>
				<a href="{{ url('admin/song_parts') }}" target="new" class="small">edit list <i class="fa fa-external-link"></i></a>
				<span class="cell-part-code hidden-md-down"></span>
			</th>


			<td class="cell-part-text">
				@include('cspot.snippets.onsong_action', ['newOnsongRow' => true])
			</td>
		</tr>


	</tbody>


</table>

<a id="tbl-bottom"></a>
