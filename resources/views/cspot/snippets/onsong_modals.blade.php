
{{-- modal to select the proper part code 
--}}
<div class="modal" id="selectSongPartCodeModal" tabindex="-1" role="dialog" aria-labelledby="selectSongPartCodeModalLabel" aria-hidden="true">
  	<div class="modal-dialog" role="document">
    	<div class="modal-content select-songpart-code">

      		<div class="modal-header">
        		<h5 class="modal-title" id="selectSongPartCodeModalLabel">Select Song-Part Name:</h5>
      		</div>

	      	<div class="modal-body">
				<a href="{{ url('admin/song_parts') }}" target="new" class="small link float-right">edit list <i class="fa fa-external-link"></i></a>

				<select id="new-onsong-part-selection" tabindex=1>
					@foreach ($songParts as $part)
						<option data-code="{{ $part->code }}" value="{{ $part->id }}">{{ $part->name }}</option>
					@endforeach
				</select>
				<div id="part-selection-error-msg" class="hidden">Select a part name!</div>
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
				Use certain elements as below to change the format and structure of the presentation to provide additional information for musicians or singers.


				<ul>
					<li class="mb-1">Simply insert an <red>empty line</red> to <strong>create individual slides</strong> in the lyrics presentations.</li>

					<li class="mb-1">For <strong>instructions to musicians</strong>, you can add lines with text enclosed in round brackets like this:<br>
						"<red>(play twice) </red>". This will only be visible to musicians.</li>

					<li class="mb-1">Text in curly braces "{}" will be shown only in the lyrics presentation as <strong>instructions for the singers</strong>,<br> 
						like "<red>{women}</red>" - for when only the women should sing that part of the song.</li>

					<li class="mb-1">In order to provide for songs where 2 groups of singers sing different lyrics at the same time, you have to create 
						a <strong>second region</strong> with just lyrics. Do so by inserting a line containing the text 'region 2' in square
						brackets like so: "<red>[region 2]</red>". Lyrics in Region 2 won't be visible for musicians.</li>

					<li>Alternatively, you could show a lyrics line in a different colour by preceding it with a comma (,). 
						Such a line also won't be shown to musicians. Additionally, for an echo, append the lyrics line with the echo lyrics in braces, 
						like so: <i>I will follow (I will follow)</i></li>

					<li class="mb-1">To hide the lyrics elsewhere from the musicians, start the line with a dot (.) </li>

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

