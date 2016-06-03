
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('files', 'active')



@section('content')


	@include('layouts.flashing')

	@if( Auth::user()->isEditor() )
		<a class="hidden-xs-up btn btn-primary-outline pull-xs-right" href='{{ url('admin/files/create') }}'>
			<i class="fa fa-plus"> </i> &nbsp; Add a new file
		</a>
	@endif

    <h2>
    	{{ $heading }}
    	<small class="text-muted">
    		<a tabindex="0" href="#"
    			data-container="body" data-toggle="popover" data-placement="bottom" data-trigger="focus"
    			data-content="files....">
    			<i class="fa fa-question-circle"></i></a>
		</small>
	</h2>


	@if (count($files))
		

		<div class="row">

	        @foreach( $files as $key => $file )
    			<div class="col-sm-12 col-md-6 col-lg-4 col-xl-2">
                    <div class="card">
                        <div class="card-block">
                            @if ( ! $item_id==0 )
                                <a href="{{ url('cspot/items').'/'.$item_id.'/addfile/'.$file->id }}" class="btn btn-sm btn-primary">
                                    select</a>
                            @else
                                <a href="#" onclick="deleteFile({{ $file->id }})" title="delete this file" 
                                    class="btn btn-sm btn-danger pull-xs-right">
                                    <i class="fa fa-trash"></i></a>
                            @endif
                            <small class="card-title">{{ $file->filename }}</small>
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
        
        <center>{!! $files->links() !!}</center>


    @else

    	No files found!

	@endif

	
@stop
