
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('files', 'active')



@section('content')


	@include('layouts.flashing')

	@if( Auth::user()->isEditor() )
		<a class="btn btn-outline-primary pull-xs-right m-l-2" href='#'
                data-toggle="modal" data-target="#fileUploadModal">
			<i class="fa fa-plus"> </i> &nbsp; Add a new file
		</a>
	@endif

    <a class="btn btn-outline-success pull-xs-right" href='#' 
            onclick="$('#show-as-large-icons').toggle();$('#show-as-filelist').toggle();">
        <i class="fa fa-list"> </i> &nbsp; Filelist / Icons
    </a>


    <h2 class="hidden-xs-down pull-xs-left m-r-2">{{ $heading }}</h2>
    <h5 class="hidden-sm-up pull-xs-left m-r-2">{{ $heading }}</h5>

	
    <select class="c-select pull-xs-left" id="fdf" onchange="selectCategory(this)">
        <option selected>Filter</option>
        <option value="newest">Show Newest</option>
        @foreach ($file_categories as $cat)
            <option value="{{ $cat->id}}">Category: {{ ucfirst($cat->name) }}</option>
        @endforeach
        <option value="*">No Filter</option>
    </select>
    <script>
        function selectCategory(that) {
            showSpinner();
            var newQueryStr = '?';
            // get current url and query string
            var currUrl = parseURLstring(window.location.href);
            if (currUrl.search.length>1) {
                var qStr = currUrl.search.split('?')[1].split('&');
                // make sure new query string potentially only contains 'item_id'
                for (var i = 0; i < qStr.length; i++) {
                    var query = qStr[i].split('=');
                    if (query[0] == 'item_id')
                        newQueryStr += qStr[i] + '&';
                }
            }
            if ( $.isNumeric($(that).val()) ) {
                location.href = currUrl.pathname + newQueryStr + "bycategory="+$(that).val();
                return;
            }
            if ($(that).val()=='newest') {
                location.href = currUrl.pathname + newQueryStr + "newest=yes";
                return;
            }
            if (newQueryStr=='?') newQueryStr='';
            location.href = currUrl.pathname + newQueryStr;
        }
    </script>

    <div class="clearfix"></div>




	@if (count($files))
		

		<div class="row" id="show-as-large-icons">


	        @foreach( $files as $key => $file )

    			<div class="col-sm-12 col-md-6 col-lg-4 col-xl-2">

                    <div class="card" id="file-{{$file->id}}" data-content="{{$file}}">

                        <div class="card-block card-block-files">
                            @if ( ! $item_id==0 )
                                <a href="{{ url('cspot/items').'/'.$item_id.'/addfile/'.$file->id }}" class="btn btn-sm btn-primary">
                                    select</a>
                            @elseif( Auth::user()->isEditor() )
                                <a href="#" onclick="deleteFile({{ $file->id }})" title="delete this file" 
                                    class="btn btn-sm btn-danger pull-xs-right">
                                    <i class="fa fa-trash"></i></a>
                                <button type="button" class="btn btn-info btn-sm pull-xs-right" data-toggle="modal" data-target="#fileEditModal"
                                    data-id="{{ $file->id }}" data-cat="{{ $file->file_category_id }}" data-filename="{{ $file->filename }}" 
                                    data-token="{{ url(config('files.uploads.webpath')).'/mini-'.$file->token }}">
                                    <i class="fa fa-pencil"></i>
                                </button>
                            @endif
                            <small class="card-title">
                                <span class="fileshow-filename-{{ $file->id }}">{{ $file->filename }}</span><br>
                                <label>Cat.:</label> <span class="fileshow-category-{{ $file->id }}">{{ $file->file_category->name }}</span>
                            </small>
                        </div>

                        <a href="{{ url(config('files.uploads.webpath')).'/'.$file->token }}">
                            <img class="card-img-top"  alt="{{ $file->filename }}" width="100%"
                                @if ( $isMobileUser )
                                    src="{{ url(config('files.uploads.webpath')).'/mini-'.$file->token }}">
                                @else
                                    src="{{ url(config('files.uploads.webpath')).'/thumb-'.$file->token }}">
                                @endif
                        </a>

                    </div>

    			</div>


				@if ( ($key+1) % 6 == 0)
					<div class="clearfix hidden-lg-down"></div>
				@endif

				@if ( ($key+1) % 3 == 0)
					<div class="clearfix hidden-xl-up"></div>
				@endif

				@if ( ($key+1) % 2 == 0)
					<div class="clearfix hidden-md-up"></div>
				@endif


	        @endforeach


		</div><!-- row -->

        

        <div id="show-as-filelist" style="display: none;">

            <table class="table table-striped
                    @if(count($files)>15)
                     table-sm
                    @endif
                ">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th class="hidden-xs-down">Category</th>
                        <th colspan="2" class="center p-r-2">Linked to<br><span class="pull-xs-left">Song</span> or <span class="pull-xs-right">Event Item</span></th>
                        <th class="center">Size</th>
                        @if( Auth::user()->isEditor() )
                            <th>Modify</th>
                        @endif
                    </tr>
                </thead>


                <tbody>

                    @foreach( $files as $key => $file )

                        <tr>

                            <td><span onclick="$('#edit-button-{{ $file->id }}').click()" class="link fileshow-filename-{{ $file->id }}"
                                title="Preview" data-toggle="tooltip" data-placement="right" data-template='
                                    <div class="tooltip" role="tooltip">
                                        <div class="tooltip-arrow"></div>
                                        <pre class="tooltip-inner"></pre>
                                        <img src="{{ url(config('files.uploads.webpath')).'/thumb-'.$file->token }}">
                                    </div>'>
                                {{ $file->filename }} <i class="fa fa-edit"> </i></span></td>

                            <td onclick="$('#edit-button-{{ $file->id }}').click()" class="link fileshow-category-{{ $file->id }} hidden-xs-down">
                                {{ $file->file_category->name }} <i class="fa fa-edit"> </i></td>

                            <td>
                                <a title="Song Details" href="{{ route('songs.edit', $file->song_id) }}">{{ $file->song_id }}</a>
                            </td>

                            <td class="pull-xs-right p-r-2">
                                @if ($file->item_id)
                                    <a title="Item Details" href="{{ route('cspot.items.edit',[ 0, $file->item_id]) }}">{{ $file->item_id }}</a>
                                @endif
                            </td>

                            <td class="center">{{ humanFileSize($file->filesize) }}</td>

                            @if( Auth::user()->isEditor() )
                                <td>
                                    <a href="#" onclick="deleteFile({{ $file->id }})" title="delete this file" 
                                        class="btn btn-sm btn-danger">
                                        <i class="fa fa-trash"></i></a>
                                    <button type="button" id="edit-button-{{ $file->id }}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#fileEditModal"
                                        data-id="{{ $file->id }}" data-cat="{{ $file->file_category_id }}" data-filename="{{ $file->filename }}" 
                                        data-token="{{ url(config('files.uploads.webpath')).'/mini-'.$file->token }}">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                </td>
                            @endif
                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>{{-- show-as-filelist --}}



        <center>{!! $files->links() !!}</center>




    @else

    	No files found!

	@endif



<script>
    /* 
        populate the modal popup when it's launched, with the data provided by the launching button ....
    */
    $('#fileEditModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var id       = button.data('id');    // Extract info from data-* attributes
        var cat      = button.data('cat');
        var filename = button.data('filename');
        var token    = button.data('token');
        // Update the modal's content
        var modal = $(this);
        modal.find('.modal-title').text('Edit File Information for ' + filename);
        modal.find('#file-id').val(id);
        modal.find('#filename').val(filename);
        modal.find('img').attr('src',token);
        modal.find('#file_category_id').val(cat);
    });

    $('#fileUploadModal').on('show.bs.modal', function (event) {
        resetAddFilesElement();
    });

</script>


@stop


<!-- Modal to edit file information -->
<div class="modal fade" id="fileEditModal" tabindex="-1" role="dialog" aria-labelledby="fileEditModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="fileEditModalLabel">Edit File Information</h4>
            </div>
            <div class="modal-body">
                <img src="" class="pull-xs-right" alt="image">
                <label>File Name</label>
                <br>
                <input type="text" id="filename">
                <br>
                <input type="hidden" id="file-id" data-action-url="{{ url('cspot/files') }}/">
                <br>
                <label>File Category</label>
                <br>
                <select name="file_category_id" id="file_category_id">
                    @foreach ( DB::table('file_categories')->get() as $fcat)
                        <option value="{{ $fcat->id }}">{{ $fcat->name }}</option>
                    @endforeach                        
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateFileInformation()">Save changes</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal to ADD (upload) new file -->
<div class="modal fade" id="fileUploadModal" tabindex="-1" role="dialog" aria-labelledby="fileUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="fileUploadModalLabel">Upload File</h4>
            </div>
            <div class="modal-body">
                @include ('cspot.snippets.add_files', ['modal' => 'files_upload'])
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
