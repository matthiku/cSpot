
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('setup', 'active')




@section('content')


    @php
        $versionName = $verses->first()->bibleversion->name;
        $versionID  = $verses->first()->bibleversion->id;
        $bookName  = $verses->first()->biblebook->name;
        $bookID   = $verses->first()->biblebook->id;
        $chapter = $verses->first()->chapter;
    @endphp


    @include('layouts.flashing')



    <nav class="navbar navbar-toggleable-sm navbar-light bg-faded">
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarBiblesText" 
            aria-controls="navbarBiblesText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="#">{{ $bookName . ' ' . $chapter . ' (' . $versionName . ')' }}</a>

        <div class="collapse navbar-collapse" id="navbarBiblesText">
            <form class="form-inline my-2 my-lg-0 ml-auto">
            
                {{-- Create a Drop-Down Selection for the chapters --}}
                <div class="dropdown float-right mr-2">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Select Chapter...
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @for ($i = 0; $i < $chapters; $i++)
                            @if ($i%5==0)
                                {!! $i>4 ? '</div>' : '' !!}
                                <div class="btn-group mx-2" role="group" aria-label="First group">
                            @endif
                            <button type="button" class="btn btn-secondary"{{ $i+1 == $chapter ? ' disabled' : '' }}
                                onclick="showSpinner();location.href=location.pathname+'?version={{ Request::get('version') }}&book={{ $bookID }}&chapter={{ $i+1 }}'" 
                                style="min-width: {{ $chapters>99 ? '62' : ($chapters>9 ? '52' : '45') }}px;">{{ 
                                    $i+1 }}</button>
                        @endfor
                        </div>
                    </div>
                </div>


                {{-- DropDown Selection of all books --}}
                <select class="custom-select float-right mr-2" onchange="showSpinner();location.href=location.pathname+'?version={{ Request::get('version') }}&book='+this.value">
                    <option selected>Select Book...</option>
                    @foreach ($books as $book)
                        <option value="{{ $book->id }}"{{ $book->id == $bookID ? 'disabled' : '' }}>{{ 
                            $book->name }}</option>
                    @endforeach
                </select>

                {{-- DropDown selection of all available Versions --}}
                <select class="custom-select float-right mr-2" onchange="showSpinner();location.href=location.pathname+'?version='+this.value">
                    <option selected>Select Version...</option>
                    @foreach ($versions as $version)
                        <option value="{{ $version->id }}"{{ $version->id == $versionID ? 'disabled' : '' }}>{{ 
                            $version->name }}</option>
                    @endforeach
                </select>

            </form>
        </div>
    </nav>




    @if ($verses->count())
                

        <div class="mw-60 ml-lg-3 pt-2">

            @foreach ($verses as $verse)
                <p class="mb-1"><sup>{{ $verse->verse }}</sup> 
                    <span class="hover-show{{ Auth::user()->isEditor() ? ' editable-bible-text' : '' }}" id="verse-{{ $versionID.'-'.$bookID.'-'.$chapter.'-'.$verse->verse }}"
                    >{{ $verse->text }}</span>
                    @if (Auth::user()->isEditor())
                        <span class="hover-only fa fa-pencil text-muted"></span>
                    @endif
                </p>
            @endforeach

        </div>

    @else
        No verses found for this version in the storage!
    @endif

@stop