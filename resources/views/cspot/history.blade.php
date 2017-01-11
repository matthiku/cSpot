
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('setup', 'active')



@section('content')


	@include('layouts.flashing')


    <h2 class="float-left mr-2">
    	{{ $heading }}
	</h2>

	@if ( Request::has('plan_id') )
		<button type="button" class="btn btn-outline-primary"
			 onclick="location.href='{{ url('cspot/history') }}'">
			Show All History
		</button>
	@endif


	@if (count($histories))

		<table class="table table-striped table-bordered 
					@if(count($histories)>5)
					 table-sm
					@endif
					 ">
			<thead class="thead-default">
				<tr>
					<th>Id</th>
					<th>User</th>
					<th>Plan No.</th>
					<th>Activity</th>
					<th>Reason</th>
					<th>Changed on</th>
				</tr>
			</thead>

			<tbody>
	        @foreach( $histories as $history )
				<tr>
					<th scope="row">{{ $history->id }}</th>

					<td>{{ $history->user->name }}</td>

					<td class="link" onclick="location.href='{{ route('plans.show', $history->plan_id) }}'"
						title="Show this plan">{{ $history->plan_id }}</td>

					<td class="white-space-pre-wrap">{{ $history->changes }}</td>

					<td>{{ $history->reason }}</td>

					<td>{{ $history->updated_at->formatLocalized('%d-%b-%y %H:%M')  }}</td>

				</tr>

	        @endforeach
			</tbody>
		</table>

    @else

    	No history records found! 

	@endif

	
@stop
