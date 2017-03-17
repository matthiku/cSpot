{{-- 
	render bible verses from server-based storage 
	and allow users with appropriate rights to edit them
 --}}


@php
	// at this moment, we do not support multiple references within one Plan Item (we only use the first)
	if (gettype($verses)=='array')
		$verses = $verses[0];

    $versionID = $verses->first()->bibleversion->id;
    $bookID   = $verses->first()->biblebook->id;
    $chapter = $verses->first()->chapter;
@endphp


@foreach ($verses as $verse)
    <p class="mb-1"><sup>{{ $verse->verse }}</sup> 
        <span class="hover-show{{ Auth::user()->isEditor() ? ' editable-bible-text' : '' }}" id="verse-{{ $versionID.'-'.$bookID.'-'.$chapter.'-'.$verse->verse }}"
        >{{ $verse->text }}</span>
        @if (Auth::user()->isEditor())
            <span class="hover-only fa fa-pencil text-muted"></span>
        @endif
    </p>
@endforeach

