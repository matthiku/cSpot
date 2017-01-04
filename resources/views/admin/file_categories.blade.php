
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('items', 'active')



@section('content')


	@include('layouts.flashing')

	@if( Auth::user()->isEditor() )
	<a class="btn btn-outline-primary float-xs-right" href="{{ url('admin/file_categories/create') }}">
		<i class="fa fa-plus"> </i> &nbsp; Add a new File Category
	</a>
	@endif

    <h2>
    	{{ $heading }}
    	<small class="text-muted">
    		<a tabindex="0" href="#"
    			data-container="body" data-toggle="popover" data-placement="bottom" data-trigger="focus"
    			data-content="File Categories help organizing attached items or images.">
    			<i class="fa fa-question-circle"></i></a>
		</small>
	</h2>
	<small>In order to show images with their original width/height aspect ractio, you should have and use a category with the name <strong>'Presentation'</strong> (case is ignored!).</small>


	@if (count($file_categories))

		<table class="table table-striped table-bordered {{ (count($file_categories)>15) ? 'table-sm' : '' }}">
			<thead class="thead-default">
				<tr>
					<th>#</th>
					<th>Name</th>
					<th>Count</th>
					<th class="center">Action</th>
				</tr>
			</thead>
			<tbody>

	        @foreach( $file_categories as $file_category )
				<tr>
					<td scope="row">
						{{ $file_category->id }}
					</td>

					<td class="link" onclick="location.href ='{{ url('admin/file_categories/' . $file_category->id) }}/edit'">
						{{ $file_category->name }}
					</td>

					<td scope="row">
						{{ $file_category->files()->count() }}
						<a href="{{ url('admin/file_categories/'.$file_category->id) }}">
							<small>(show)</small></a>
					</td>

					<td class="nowrap center">
							<a class="btn btn-secondary hidden-sm-down btn-sm" title="Show Files of this category" 
								href='{{ url('cspot/files').'?bycategory='.$file_category->id }}'>
								<i class="fa fa-filter"></i></a>
						 @if ( Auth::user()->isEditor() )
							<a class="btn btn-outline-primary btn-sm hidden-xs-down" title="Edit" 
								href="{{ url('admin/file_categories/'.$file_category->id) }}/edit"><i class="fa fa-pencil"></i></a>
							@if ($file_category->id>1)
								<a class="btn btn-danger btn-sm" title="Delete!" 
									href="{{ url('admin/file_categories/'.$file_category->id) }}/delete"><i class="fa fa-trash"></i></a>
							@else
								<a tabindex="0" href="#" class="ml-1" 
					    			data-container="body" data-toggle="popover" data-placement="bottom" data-trigger="focus"
					    			data-content="system default values cannot be deleted!">
					    			<i class="fa fa-question-circle"></i></a>
							@endif
						@endif
					</td>					
				</tr>
	        @endforeach
			</tbody>
		</table>

    @else

    	No File Category found!

	@endif

	
@stop
