
<!-- needs a '$file' value from the parent view -->

<figure class="figure" id="file-{{ $file->id }}">

    <a href="{{ url(config('files.uploads.webpath')).'/'.$file->token }}">
        <img style="max-width:250px;" class="figure-img img-fluid img-rounded img-thumbnail" 
            src="{{ url(config('files.uploads.webpath')).'/'.$file->token }}">
    </a>

    <figcaption class="figure-caption">
    	{{ $file->filename }}
        <a href="#" onclick="deleteFile({{ $file->id }})"><i class="fa fa-trash red"></i> Delete this file</a>
    </figcaption>

</figure>
