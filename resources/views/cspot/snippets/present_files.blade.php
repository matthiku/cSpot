
<figure class="figure" id="file-{{ $file->id }}">

    <a href="{{ url(config('files.uploads.webpath')).'/'.$file->token }}">

        <img class="figure-img img-fluid img-rounded full-width" 
               src="{{ url(config('files.uploads.webpath')).'/'.$file->token }}">
    </a>

</figure>
