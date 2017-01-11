
<!-- bootstrap card element to select a new file for upload -->

<div class="card">


    @if (! isset($modal)) 
        <div class="card-block">
            <h4 class="card-title">
                <span class="big link float-right" title="Close" onclick="
                    $('#col-2-file-add').hide();
                    $('.add-another-image-link').show();
                    ">&#128502;</span>
                Add an image
            </h4> 
        </div>
    @endif


    <ul class="list-group list-group-flush">



        {{-- select a category 
        --}}
        <li class="list-group-item modal-select-file center">

            {!! Form::label('file_category_id', 'Select a category: ') !!}

            <div class="btn-group modal-select-file ml-1" data-toggle="buttons"
                @if (! isset($modal)) 
                        onclick="
                            $('#show-location-selection').show();
                            $('.image-selection-slideshow').hide();
                            $('.show-file-add-button').hide();
                            location.href='#upload-or-select';" 
                @endif
                >

                {{-- different selection modes depending on context --}}
                @if (isset($modal))    

                    <select name="file_category_id" id="file_category_id" onchange="showLocalVersusRemoteButtons(this, '{{ $modal }}')">
                        <option selected="TRUE" value="">select ...</option>
                        @if ($modal != 'files_upload')
                            <option value="newest">recently added</option>
                        @endif
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
            
            <p class="mt-1 show-selected-category hidden">Selected category: <span class="text-info show-selected-category"></span></p>

            <a id="select-category"></a>
        </li>




        {{-- upload a new one or select an existing file? 
            (we must use style="display: none;" here because when showing it, the inherited value would be "list-item")
        --}}
        <li class="list-group-item center" id="show-location-selection" style="display: none;">

            <label class="card-text">
                Do you want to upload a new image from your device or<br>select an image that was already uploaded?
            </label>

            <button type="button" class="btn btn-primary btn-sm mr-1" id="btn-upload-new-image"
                onclick="
                    $(this).parent().hide();
                    $('.image-selection-slideshow').hide();
                    $('.show-file-add-button').show();
                    location.href='#bottom';"
                    >
                Upload new image</button>

            <button type="button" class="btn btn-secondary btn-sm ml-1" id="btn-select-cspot-images"
                data-ajax-url="{{ route('cspot.api.files') }}"
                data-images-path="{{ url(config('files.uploads.webpath')) }}"
                onclick="
                    $(this).parent().hide();
                    $('.show-file-add-button').hide();
                    showImagesSelection(this);
                    ">
                Select c-SPOT images</button>

            <a id="upload-or-select"></a>
        </li>




        {{-- show images 
        --}}
        <li class="list-group-item image-selection-slideshow center" style="display: none;">

            <p class="mt-1">Selected category: <span class="text-info show-selected-category"></span></p>

            <label class="card-text" id="images-for-selection-label"></label>
            <br>

            <a disabled="" class="show-next-image-arrows" onclick="showNextImages('back');location.href='#bottom';"><i class="fa fa-caret-left fa-3x link"></i></a>

            <span id="show-images-for-selection"></span>

            <a disabled="" class="show-next-image-arrows" onclick="showNextImages('forw');location.href='#bottom';"><i class="fa fa-caret-right fa-3x link"></i></a>

            <p class="card-text text-muted" id="link-to-more-images"></p>

        </li>




        {{-- show file  UPLOAD  button 
        --}}
        <li class="list-group-item show-file-add-button"  style="display: none;">
            
            {!! Form::label('file', 'Select a file to be uploaded: ') !!}
            <br>
            {!! Form::file('file'); !!}    
            <span class="card-text"><small>(Maximum file size: <?php echo ini_get("upload_max_filesize"); ?>)!</small></span>

            {{-- don't show the submit button on the popup modal form as we handle the upload via AJAX --}}
            @if (! isset($modal)) 
                <span class="show-file-add-button float-right hidden">
                    {!! Form::submit('Submit') !!}                                
                </span>
            @endif
        </li>


    </ul>
</div>

<a id="bottom"></a>
