
<!-- bootstrap card element to select a new file for upload -->

<div class="card">


    @if (! isset($modal)) 
        <div class="card-block">
            <h4 class="card-title">Add an image</h4> 
        </div>
    @endif


    <ul class="list-group list-group-flush">



        {{-- select a category 
        --}}
        <li class="list-group-item modal-select-file center">

            {!! Form::label('file_category_id', 'Select a category: ') !!}

            <div class="btn-group modal-select-file m-l-1" data-toggle="buttons"
                @if (! isset($modal)) 
                    onclick="$('#show-location-selection').show()" 
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
            
            <p class="m-t-1">Selected category: <span class="text-info show-selected-category"></span></p>

        </li>




        {{-- upload a new one or select an existing file? 
        --}}
        <li id="show-location-selection" class="list-group-item center" style="display: none;">

            <label class="card-text">
                Do you want to upload a new image from your device or<br>select an image that was already uploaded?
            </label>

            <button type="button" class="btn btn-primary btn-sm m-r-1" id="btn-upload-new-image"
                onclick="$(this).parent().hide();$('.show-file-add-button').show()"
                    >Upload new image</button>

            <button type="button" class="btn btn-secondary btn-sm m-l-1" id="btn-select-cspot-images"
                data-ajax-url="{{ route('cspot.api.files') }}"
                data-images-path="{{ url(config('files.uploads.webpath')) }}"
                onclick="$(this).parent().hide();showImagesSelection(this)">Select c-SPOT images</button>

        </li>




        {{-- show images 
        --}}
        <li class="list-group-item image-selection-slideshow center" style="display: none;">

            <p class="m-t-1">Selected category: <span class="text-info show-selected-category"></span></p>

            <label class="card-text" id="images-for-selection-label"></label>
            <br>

            <a href="#" disabled="" class="show-next-image-arrows" onclick="showNextImages('back')"><i class="fa fa-caret-left fa-3x"></i></a>

            <span id="show-images-for-selection"></span>

            <a href="#" disabled="" class="show-next-image-arrows" onclick="showNextImages('forw')"><i class="fa fa-caret-right fa-3x"></i></a>

            <p class="card-text text-muted" id="link-to-more-images"></p>

        </li>




        {{-- show file  UPLOAD  button 
        --}}
        <li class="list-group-item show-file-add-button" style="display: none;">
            
            {!! Form::label('file', 'Select a file to be uploaded: ') !!}
            <br>
            {!! Form::file('file'); !!}    
            <span class="card-text"><small>(Maximum file size: <?php echo ini_get("upload_max_filesize"); ?>)!</small></span>

            {{-- don't show the submit button on the popup modal form as we handle the upload via AJAX --}}
            @if (! isset($modal)) 
                <span class="show-file-add-button pull-xs-right" style="display: none;">
                    {!! Form::submit('Submit') !!}                                
                </span>
            @endif
        </li>


    </ul>
</div>
