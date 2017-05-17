
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('setup', 'active')



@section('content')


	@include('layouts.flashing')

	@if( Auth::user()->isEditor() )
		<a class="btn btn-outline-primary float-right" href='{{ url('admin/bibleversions/create') }}'>
			<i class="fa fa-plus"> </i> &nbsp; Add a new Version
		</a>
	@endif

    <h2>
    	{{ $heading }}
	</h2>


	@if (count($bibleversions))

		<table class="table table-striped table-bordered{{ count($bibleversions)>15 ? ' table-sm' : '' }}">
			<thead class="thead-default">
				<tr>
					<th>id</th>
					<th>Name</th>
					<th># Books</th>
					<th># Verses</th>
					<th>Action</th>
				</tr>
			</thead>


			<tbody>

	        @foreach( $bibleversions as $bibleversion )

	        	@php
	        		$verseCount = $bibleversion->bibles->count();
	        		$books      = $bibleversion->books;
	        		$booksCount = count($books);
        		@endphp

				<tr>

					<td scope="row">{{ $bibleversion->id }}</td>

					@if( Auth::user()->isEditor() )
						<td class="link" title="Edit this name" onclick="showSpinner();location.href='{{ url('/admin/bibleversions/' . $bibleversion->id) }}/edit'">{{ $bibleversion->name }}</td>
					@else
						<td>
							<a class="btn btn-secondary btn-sm" title="Show Books in this Version" href='{{ url('admin/biblebooks?version='.$bibleversion->id) }}'>
								{{ $bibleversion->name }}</a>
						</td>
					@endif

					<td>@if ($verseCount>0)
							<a class="btn btn-secondary" title="Show Books in this Version" href='{{ url('admin/biblebooks?version='.$bibleversion->id) }}'>
								{{ $booksCount }}
							</a>
			                {{-- DropDown Selection of all books --}}
			                <select class="custom-select float-right mr-2"
			                		onchange="showSpinner();location.href='{{ url('admin/bibles') }}?version={{ $bibleversion->id }}&book='+this.value">
			                    <option selected>Open Book...</option>
			                    @foreach ($books as $key => $book)
			                        <option value="{{ $key+1 }}">{{
			                            $book }}</option>
			                    @endforeach
			                </select>

						@else
							{{ $booksCount }}
						@endif
					</td>

					<td>@if ($verseCount>0)
							<a class="btn btn-secondary" title="Show Books in this Version" href='{{ url('admin/biblebooks?version='.$bibleversion->id) }}'>
								{{ $verseCount }}
							</a>
							</a>
						@else
							0
						@endif
					</td>

					<td class="nowrap">
						@if( Auth::user()->isEditor() )
							<a class="btn btn-outline-primary" title="Edit" href='{{ url('admin/bibleversions/'.$bibleversion->id) }}/edit'><i class="fa fa-pencil"></i></a>
							@if ($verseCount==0)
								<a class="btn btn-danger btn-sm" title="Delete!" href='{{ url('admin/bibleversions/'.$bibleversion->id) }}/delete'><i class="fa fa-trash"></i></a>
							@endif
						@endif
					</td>

				</tr>
	        @endforeach
			</tbody>
		</table>

    @else

    	No bibleversions found! Add one ...

	@endif


@stop
