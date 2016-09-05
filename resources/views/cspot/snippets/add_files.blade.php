
<!-- bootstrap card element to select a new file for upload -->

<div class="card">

    <div class="card-block">
        @if (! isset($modal)) 
            <h4 class="card-title">Add an image</h4> 
        @endif
        <span class="card-text"><small>(Maximum file size: <?php echo ini_get("upload_max_filesize"); ?>)!</small></span>
    </div>

    <ul class="list-group list-group-flush">


        <li class="list-group-item">

            {!! Form::label('file_category_id', 'First, select a category for this file: ') !!}
            <br>
            <div class="btn-group modal-select-file" data-toggle="buttons"
                @if (! isset($modal)) onclick="$('.show-file-add-button').show()" @endif>

                {{-- different selection modes depending on context --}}
                @if (isset($modal))    
                    <select name="file_category_id" id="file_category_id" onchange="$('.show-file-add-button').show()">
                        <option selected="TRUE" value="">select ...</option>
                        @foreach ( DB::table('file_categories')->get() as $fcat)
                            <option value="{{ $fcat->id }}">{{ $fcat->name }}</option>
                        @endforeach                        
                    </select>
                @else
                    @foreach ( DB::table('file_categories')->get() as $fcat)
                        <label class="btn btn-primary">
                            <input type="radio" name="file_category_id" id="option-{{ $fcat->id }}" autocomplete="off" value="{{ $fcat->id }}">{{ $fcat->name }}
                        </label>
                    @endforeach
                @endif

            </div>
        </li>


        <li class="list-group-item show-file-add-button" style="display: none;">
            {!! Form::label('file_category_id', 'Next, select a file to be uploaded: ') !!}
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
