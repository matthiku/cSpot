
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@if (Request::is('*/future'))
	@section('future', 'active')
@else
	@section('plans', 'active')
@endif


@section('content')


	@include('layouts.flashing')
	


	@if( Auth::user()->isEditor() )
	<a class="btn btn-primary-outline pull-xs-right" href="{{ url('cspot/plans/create') }}">
		<i class="fa fa-plus"> </i> &nbsp; Add a new plan
	</a>
	@endif

    <h2 class="pull-xs-left">
    	{{ $heading }}
		@if( Request::has('show') )
			<br>
			<small class="pull-xs-left" style="font-size: 50%">
				<a href="#" onclick="toogleAllorFuturePlans()">
					<input type="checkbox" {{Request::get('show')=='all' ? '' : 'checked'}}>
					show only upcoming service plans</a>
			</small>
		@endif
    </h2>

	<center>Page {{ $plans->currentPage() }} of {{ $plans->lastPage() }}</center>



	@if ( isset($plans) && count($plans) )
		<center><small>(Total: {{ $plans->total() }} Service Plans)</small></center>

		<table class="table table-striped table-bordered table-hover
					@if(count($plans)>15)
					 table-sm
					@endif
					 ">
			<thead class="thead-default">
				<tr>
					<th class="hidden-md-down center">#</th>

					@include('cspot.snippets.theader', ['thfname' => 'date', 'thdisp' => 'Date', 'thsort'=>false, 'thclass'=>''])
					
					@include('cspot.snippets.theader', ['thfname' => 'type_id', 'thdisp' => 'Service Type', 'thsort'=>false, 'thclass'=>''])

					<th class="hidden-sm-down center"># items</th>

					@include('cspot.snippets.theader', ['thfname' => 'leader_id', 'thdisp' => 'Leader', 'thsort'=>false, 'thclass'=>'hidden-xs-down center'])

					@include('cspot.snippets.theader', ['thfname' => 'teacher_id', 'thdisp' => 'Teacher', 'thsort'=>false, 'thclass'=>'hidden-xs-down center'])

					<th class="hidden-sm-up center">Leader, Teacher</th>
					<th class="text-right hidden-md-down">Last updated on</th>
					<th class="hidden-md-down">by</th>
				</tr>
			</thead>


			<tbody>
	        @foreach( $plans as $plan )
				<tr class="link" onclick="location.href='{{ url('cspot/plans/'.$plan->id) }}/edit'"
					data-toggle="tooltip" title="Click on/touch a plan to view/edit it">

					<td class="hidden-md-down center" scope="row">{{ $plan->id }}</td>

					<td class="hidden-md-down">{{ $plan->date->formatLocalized('%A, %d %B %Y') }}</td>
					<td class="hidden-sm-down hidden-lg-up">{{ $plan->date->formatLocalized('%a, %d %B %Y') }}</td>
					<td class="hidden-md-up">{{ $plan->date->formatLocalized('%a, %d %b') }}</td>

					<td>{{ $plan->type->name }}</td>

					<td class="hidden-sm-down center">{{ $plan->items->count() }}</td>
					<td class="hidden-xs-down center">{{ $plan->leader->first_name }}</td>
					<td class="hidden-xs-down center">{{ $plan->teacher->first_name }}</td>
					<td class="hidden-sm-up center">
						{{ $plan->leader->first_name }}{{ $plan->teacher_id<>0 ? ', '.$plan->teacher->first_name : '' }}
					</td>

					<td class="hidden-md-down text-right">
						{{ isset($plan->updated_at) ? $plan->updated_at->formatLocalized('%d-%m-%Y %H:%M') : 'unknown' }}</td>
					<td class="hidden-md-down">{{ ucfirst($plan->changer) }}</td>

				</tr>
	        @endforeach

			</tbody>

		</table>

		<center>
			{!! $plans->links() !!}
		</center>
		<script>
			// add missing classes and links into the auto-geneerated pagination buttons
			$('.pagination').children().each(function() { $(this).addClass('page-item'); });
			$('.page-item>a').each(function() { $(this).addClass('page-link'); });
			var pgActive = $('.active.page-item').html();
			$('.active.page-item').html('<a class="page-link" href="#">'+pgActive+'</a>');
			$('.disabled.page-item').each(function() {
				var innerHtml = $(this).html();
				$(this).html('<a class="page-link" href="#">'+innerHtml+'</a>');
			})
		</script>

    @else

    	<p>No plans found!</p>

	@endif

	
@stop
