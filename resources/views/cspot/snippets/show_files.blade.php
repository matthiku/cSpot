
<!-- needs a '$file' value from the parent view -->

<?php if (!isset($edit)) $edit=true; ?>

<figure class="figure pt-1 mb-1" id="file-figure-{{ $file->id }}">

    <div class="float-left">    
        <!-- show thumbnail, but link to full image -->
        <a href="{{ url(config('files.uploads.webpath')).'/'.$file->token }}">
            <img style="max-width:250px;" class="mb-0 figure-img img-fluid img-rounded img-thumbnail" 
                @if ( $isMobileUser )
                    src="{{ url(config('files.uploads.webpath')).'/mini-'.$file->token }}">
                @else
                    src="{{ url(config('files.uploads.webpath')).'/thumb-'.$file->token }}">
                @endif
        </a>
        <div>
            <small class="text-muted" title="Category">{{ isset($song->id) ? '' : $file->file_category->name.' -' }}</small>
            {{ $file->filename }}
        </div>
    </div>

    @if ($edit)
        <div class="float-left ml-1">
            @if ( isset($fcount) && $fcount>1)
                <div class="text-center"><small>Seq.No.</small><br><big class="text-success">{{ $file->pivot->seq_no }}</big></div>
            @endif

            <div class="text-right mt-1">
                @if (isset($item->id))        
                    {{-- check if user is leader of the corresponding plan or author/admin --}}
                    @if ( $item->plan->leader_id==Auth::user()->id || Auth::user()->isAuthor() )
                        <a class="btn-info rounded p-1 small" href="#" onclick="unlinkFile({{ $item->id }},{{ $file->id }})">
                            <i class="fa fa-unlink" title="Unlink this file from this item"></i>
                            Unlink</a>
                    @endif
                @endif
                {{-- show unlink button if user has at least Author rights for songs --}}
                @if ( isset($song->id)  &&  Auth::user()->isAuthor() )
                        <a class="btn-info rounded p-1 small" href="#" onclick="unlinkSongFile({{ $song->id }},{{ $file->id }})">
                            <i class="fa fa-unlink" title="Unlink this file from this song"></i>
                            Unlink</a>
                @endif
            </div>

            <div class="text-right my-1">
                {{-- check if user is leader of the corresponding plan or author/admin --}}
                @if ( ! isset($item->id) || Auth::user()->isAuthor()  ||  (isset($item->id) && $item->plan->leader_id==Auth::user()->id) )        
                    <a class="btn-info rounded p-1 small" href="#" onclick="deleteFile({{ $file->id }})">
                        <i class="fa fa-trash red" title="Delete this file from storage and the database"></i>
                        Delete</a>
                @endif
            </div>

            <div class="small text-center">
                Size:
                <br>{!! $file->filesize ? humanFileSize($file->filesize) : '<small>unknown</small>' !!}
            </div>
        </div>
    @endif


</figure>
