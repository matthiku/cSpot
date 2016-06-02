
<!-- needs a '$file' value from the parent view -->

<figure class="figure" id="file-{{ $file->id }}">

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
        <a class="pull-md-right" href="#" onclick="deleteFile({{ $file->id }})"><i class="fa fa-trash red"></i> Delete this file</a>
    	{{ $file->filename }}
    </figcaption>

</figure>
