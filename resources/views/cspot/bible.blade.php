
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
        <a class="navbar-brand show-bible-text-brand" href="{{ route('bibleversions.index') }}">{{
            Request::has('search') ? 'Searching ... ' : $bookName . ' ' . $chapter . ' (' . $versionName . ')' }}</a>

        <div class="collapse navbar-collapse" id="navbarBiblesText">
            <form class="form-inline my-2 my-lg-0 ml-auto">


                {{-- search input field --}}
                <div class="input-group float-right mr-2"
                    title="Search within the currently selected bible version.
Enter any word or words to do a full-text search throughout the whole bible.
Use '+' or '-' signs right in front of a word to indicate it must be included or excluded from the search.">
                    <input type="text" class="form-control bible-search-string" onkeydown="if (event.keyCode == 13) { fulltextBibleSearch({{ $versionID }});return false;}"
                        placeholder="Search for...">
                    <span class="input-group-btn">
                        <button class="btn btn-secondary" onclick="fulltextBibleSearch({{ $versionID }})" type="button">&#128270; Go!</button>
                    </span>
                </div>


                <div class="select-chapter-or-book">
                    {{-- Create a Drop-Down Selection for the chapters --}}
                    @if ($chapters>1)
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
                    @endif

                    {{-- DropDown Selection of all books --}}
                    <select class="custom-select float-right mr-2"
                            onchange="showSpinner();location.href=location.pathname+'?version={{ Request::get('version') }}&book='+this.value">
                        <option selected>Select Book...</option>
                        @foreach ($books as $book)
                            <option value="{{ $book->id }}"{{ $book->id == $bookID ? 'disabled' : '' }}>{{
                                $book->name }}</option>
                        @endforeach
                    </select>
                </div>


                {{-- DropDown selection of all available Versions --}}
                <select class="custom-select float-right mr-2"
                        onchange="
                            showSpinner();
                            var search = $('.bible-search-string').val();
                            var srchStr = '';
                            if (search.length)
                                srchStr = '&search=' + search;
                            location.href = location.pathname
                                                + '?version=' + this.value
                                                + '&book={{ $bookID }}&chapter={{ $chapter }}'
                                                + srchStr;">
                    <option selected>Change Version...</option>
                    <script>cSpot.bibleVersions={};</script>
                    @foreach ($versions as $version)
                        <script>cSpot.bibleVersions['{{ $version->id }}']='{{ $version->name }}';</script>
                        <option value="{{ $version->id }}"{{ $version->id == $versionID ? 'disabled' : '' }}>{{
                            $version->name }}</option>
                    @endforeach
                </select>

            </form>
        </div>
    </nav>


    <div class="append-alert-area-here mw-60 mx-auto"></div>



    <div class="show-bible-text mw-60 mx-auto pt-2">


        @if ( !Request::has('search') )

            @if ( $verses->count() )

                @include ('cspot.snippets.show_verses')

            @else
                No verses found for this version in the storage!
            @endif

        @else
            One moment ...

            {{--
            SQL code for search-and-replace:
            UPDATE `bibles` SET text = REPLACE(text, 'labor', 'labour') WHERE INSTR(text, 'labor') > 0;
            --}}
        @endif

    </div>



    <script>
        cSpot.bibleBooks    = {!! json_encode($books) !!};

        // check if we already have a search string the URL
        // if (location.search.indexOf('search=')) {
        @if (Request::has('search'))
            $('.bible-search-string').val('{{ Request::get('search') }}')
            fulltextBibleSearch({{ Request::has('version') ? Request::get('version') : '' }});
        @endif
    </script>

@stop
