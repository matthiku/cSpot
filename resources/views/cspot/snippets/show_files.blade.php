
<!-- needs a '$file' value from the parent view -->

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
        <br />
        <a class="small" href="#" onclick="deleteFile({{ $file->id }})"><i class="fa fa-trash red"></i> Delete this file</a>
        <div class="pull-xs-right">Size: {{ humanFileSize($file->filesize) }}</div>
    </figcaption>

</figure>
