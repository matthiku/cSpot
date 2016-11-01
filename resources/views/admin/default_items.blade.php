
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('items', 'active')



@section('content')


	@include('layouts.flashing')

	@if( Auth::user()->isEditor() )
	<a class="btn btn-outline-warning float-xs-right ml-1" href="{{ url('cspot/plans/create') }}
			{{ Request::has('filtervalue') ? '?type_id='.Request::get('filtervalue') : '' }}"
			title="Create a new Event of selected type (if any)">
		<i class="fa fa-plus"> </i> &nbsp; Create Event
	</a>
	<a class="btn btn-outline-primary float-xs-right ml-1" href="{{ url('admin/default_items/create') }}">
		<i class="fa fa-plus"> </i> &nbsp; Add item
	</a>
	@endif


	<a class="btn btn-outline-success float-xs-right ml-1" href="{{ url('admin/default_items') }}">
		<i class="fa fa-list"> </i> &nbsp; Show All
	</a>

	<form class="form-inline float-xs-right">
		<div class="form-group">
			<label for="typefilter">Filter by</label>
			<select class="custom-select" id="typefilter" onchange="location.href='{{url('admin/default_items')}}?filterby=type&filtervalue='+$(this).val()">
				<option {{Request::has('filtervalue') ? '' : 'selected'}}>select Event Type</option>
				@foreach ($types as $type)
					<option {{(Request::has('filtervalue') && Request::get('filtervalue')==$type->id) ? 'selected' : ''}} value="{{$type->id}}">{{$type->name}}</option>
				@endforeach
			</select>
		</div>
	</form>


    <h2>
    	{{ $heading }}
    	<small class="text-muted">
    		<a tabindex="0" href="#"
    			data-container="body" data-toggle="popover" data-placement="bottom" data-trigger="focus"
    			data-content="Default items are parts of a service or event that are always the same. They will be automatically inserted into new event plans.">
    			<i class="fa fa-question-circle"></i></a>
		</small>
	</h2>




	@if (count($default_items))

		<table class="table table-striped table-bordered 
					@if(count($default_items)>15)
					 table-sm
					@endif
					 ">
			<thead class="thead-default">
				<tr>
					<th>#</th>
					<th>Service Type</th>
					<th class="red center">Sequence No.</th>
					<th>Text</th>
					<th>Default Image</th>
					<th class="center" title="Is this item to be visible only for the Leader?">FLEO?</th>
					<th class="center">Action</th>
				</tr>
			</thead>

			<tbody>

	        @foreach( $default_items as $default_item )
				<tr class="link" onclick="location.href ='{{ url('admin/default_items/' . $default_item->id) }}/edit'">

					<td scope="row">{{ $default_item->id }}</td>

					<td>{{ $default_item->type_id.' ('.$default_item->type->name.')'  }}</td>

					<td class="red center">{{ $default_item->seq_no }}</td>

					<td>{{ $default_item->text }}</td>

					<td 
						@if ($default_item->file)
							title="Default Image" data-toggle="tooltip" data-placement="left"
							data-template='
								<div class="tooltip" role="tooltip">
									<div class="tooltip-arrow"></div>
									<pre class="tooltip-inner"></pre>
									<img src="{{ url(config('files.uploads.webpath')).'/thumb-'.$default_item->file->token }}">
								</div>'
						@endif
						>{{ $default_item->file ? $default_item->file->filename : '' }}</td>

					<td class="center">{!! $default_item->forLeadersEyesOnly 
							? '&#10004;'
							: '' !!}</td>

					<td class="nowrap center">
						 @if( Auth::user()->isEditor() )
							<a class="btn btn-outline-primary btn-sm hidden-lg-down" title="Edit" 
								href="{{ url('admin/default_items/'.$default_item->id) }}/edit"><i class="fa fa-pencil"></i></a>
							<a class="btn btn-danger btn-sm" title="Delete!" 
								href="{{ url('admin/default_items/'.$default_item->id) }}/delete"><i class="fa fa-trash"></i></a>
						@endif
					</td>

				</tr>
	        @endforeach

			</tbody>

		</table>


    @else

    	No default items found!

	@endif

@stop