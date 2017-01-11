
<!-- needs a '$file' value from the parent view -->

<?php if (!isset($edit)) $edit=true; ?>

<figure class="figure" id="file-figure-{{ $file->id }}">

    <!-- show thumbnail, but link to full image -->
    <a href="{{ url(config('files.uploads.webpath')).'/'.$file->token }}">
        <img style="max-width:250px;" class="figure-img img-fluid img-rounded img-thumbnail" 
            @if ( $isMobileUser )
                src="{{ url(config('files.uploads.webpath')).'/mini-'.$file->token }}">
            @else
                src="{{ url(config('files.uploads.webpath')).'/thumb-'.$file->token }}">
            @endif
    </a>


    <figcaption class="figure-caption">
    	{{ $file->filename }}
        <small class="text-muted">({{ $file->file_category->name }})</small>

        @if ($edit)
            <br />

            @if (isset($item->id))        
                {{-- check if user is leader of the corresponding plan or author/admin --}}
                @if ( $item->plan->leader_id==Auth::user()->id || Auth::user()->isAuthor() )
                    <a class="small mr-1" href="#" onclick="unlinkFile({{ $item->id }},{{ $file->id }})">
                        <i class="fa fa-unlink" title="Unlink this file from this item"></i>
                        Unlink</a>
                @endif
            @endif

            {{-- show unlink button if user is leader of the corresponding plan or author/admin --}}
            @if ( isset($song->id)  &&  Auth::user()->isAuthor() )
                    <a class="small mr-1" href="#" onclick="unlinkSongFile({{ $song->id }},{{ $file->id }})">
                        <i class="fa fa-unlink" title="Unlink this file from this song"></i>
                        Unlink</a>
            @endif

            {{-- check if user is leader of the corresponding plan or author/admin --}}
            @if ( ! isset($item->id) || Auth::user()->isAuthor()  ||  (isset($item->id) && $item->plan->leader_id==Auth::user()->id) )        
                <a class="small" href="#" onclick="deleteFile({{ $file->id }})">
                    <i class="fa fa-trash red" title="Delete this file from the database"></i>
                    Delete</a>
            @endif
        @endif

        <div class="float-right">Size: {{ humanFileSize($file->filesize) }}</div>

    </figcaption>


</figure>
