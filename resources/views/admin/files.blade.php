
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('files', 'active')



@section('content')


	@include('layouts.flashing')

	@if( Auth::user()->isEditor() )
		<a class="btn btn-primary-outline pull-xs-right" href='{{ url('admin/files/create') }}'>
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
				@include ('cspot.snippets.show_files')				
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
		</div>


    @else

    	No files found!

	@endif

	
@stop
