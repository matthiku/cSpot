
<div class="input-group">	

	<label for="transpose-oldkey" class="mb-0 lh-1">Select the <strong>current key</strong><br> of this song</label>

	<select id="transpose-oldkey" class="ml-2 mr-4" name="oldkey">
		<option value="SelectOldKey"> Old Key (required)
		<option value="Ab"> Ab
		<option value="A"> A
		<option value="A#"> A#
		<option value="Bb"> Bb
		<option value="B"> B
		<option value="C"> C
		<option value="C#"> C#
		<option value="Db"> Db
		<option value="D"> D
		<option value="D#"> D#
		<option value="Eb"> Eb
		<option value="E"> E
		<option value="F"> F
		<option value="F#"> F#
		<option value="Gb"> Gb
		<option value="G"> G
		<option value="G#"> G#
	</select>


	<label for="transpose-newkey" class="mb-0 lh-1">Select the desired <strong>new key</strong></label>

	<select id="transpose-newkey" class="ml-2" name="newkey">
		<option value="SelectNewKey"> New Key (required)
		<option value="Ab"> Ab
		<option value="A"> A
		<option value="A#"> A#
		<option value="Bb"> Bb
		<option value="B"> B
		<option value="C"> C
		<option value="C#"> C#
		<option value="Db"> Db
		<option value="D"> D
		<option value="D#"> D#
		<option value="Eb"> Eb
		<option value="E"> E
		<option value="F"> F
		<option value="F#"> F#
		<option value="Gb"> Gb
		<option value="G"> G
		<option value="G#"> G#
	</select>

	<button onclick="transposeSongChords();" type="button" class="btn btn-primary ml-4">Submit</button>

</div>

<div id="show-transpose-form-errors" class="center big text-danger"></div>
