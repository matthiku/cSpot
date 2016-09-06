
<!-- bootstrap card element to select a new file for upload -->

<div class="card">


    @if (! isset($modal)) 
        <div class="card-block">
            <h4 class="card-title">Add an image</h4> 
            <span class="card-text"><small>(Maximum file size: <?php echo ini_get("upload_max_filesize"); ?>)!</small></span>
        </div>
    @endif


    <ul class="list-group list-group-flush">



        {{-- select a category 
        --}}
        <li class="list-group-item">

            {!! Form::label('file_category_id', 'First, select a category: ') !!}

            <div class="btn-group modal-select-file" data-toggle="buttons"
                @if (! isset($modal)) onclick="$(this).hide();$('#show-location-selection').show()" @endif>

                {{-- different selection modes depending on context --}}
                @if (isset($modal))    

                    <select name="file_category_id" id="file_category_id" onchange="$(this).parent().parent().hide();$('#show-location-selection').show()">
                        <option selected="TRUE" value="">select ...</option>
                        <option value="newest">recently added</option>
                        @foreach ( DB::table('file_categories')->get() as $fcat)
                            <option value="{{ $fcat->id }}">{{ $fcat->name }}</option>
                        @endforeach                        
                    </select>

                @else
                    <br>
                    @foreach ( DB::table('file_categories')->get() as $fcat)
                        <label class="btn btn-primary">
                            <input type="radio" name="file_category_id" id="option-{{ $fcat->id }}" autocomplete="off" value="{{ $fcat->id }}">{{ $fcat->name }}
                        </label>
                    @endforeach

                @endif

            </div>

        </li>




        {{-- upload a new one or select an existing file? 
        --}}
        <li id="show-location-selection" class="list-group-item" style="display: none;">

            <label>
                Do you want to upload a new image from your device or<br>select an image already uploaded?
            </label>

            <button type="button" class="btn btn-primary btn-sm" 
                onclick="$(this).parent().hide();$('.show-file-add-button').show()"
                    >Upload new image</button>

            <button type="button" class="btn btn-secondary btn-sm" 
                data-ajax-url="{{ route('cspot.api.files') }}"
                data-images-path="{{ url(config('files.uploads.webpath')) }}"
                onclick="$(this).parent().hide();showImagesSelection(this)">Select c-SPOT images</button>
        </li>




        {{-- show file selection button 
        --}}
        <li class="list-group-item image-selection-slideshow" style="display: none;">
            <a href="#">&lt;</a>
            <span id="show-images-for-selection"></span>
            <a href="#">&gt;</a>
        </li>




        {{-- show file selection button 
        --}}
        <li class="list-group-item show-file-add-button" style="display: none;">

            {!! Form::label('file', 'Now, select a file to be uploaded: ') !!}
            <br>
            {!! Form::file('file'); !!}    

            {{-- don't show the submit button on the popup modal form as we handle the upload via AJAX --}}
            @if (! isset($modal)) 
                <span class="show-file-add-button pull-xs-right" style="display: none;">
                    {!! Form::submit('Submit') !!}                                
                </span>
            @endif
        </li>


    </ul>
</div>
