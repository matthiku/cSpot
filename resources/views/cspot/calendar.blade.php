
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')


@section('title', $heading)


@if (Request::is('*/future'))
	@section('future', 'active')
@else
	@section('plans', 'active')
@endif


@section('content')


	<div class="events-table calendar-container">
	
		@include ('cspot.snippets.events_calendar')	

	</div>

 
@stop