
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('setup', 'active')




@section('content')


    @include('layouts.flashing')

    
    <h2>{{ $heading }}</h2>

    <h3>{{ $verses->first()->biblebook->name . ' ' . $verses->first()->chapter }}</h3>

    <ul>        
        @foreach ($verses as $verse)
            <li>
                {{ $verse->verse . ': ' . $verse->text }}
            </li>
        @endforeach
    </ul>

@stop