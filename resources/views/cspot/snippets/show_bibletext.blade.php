
<div class="bible-text-present" id="bible-text-present-all" 
     style="{{ $positioned=='yes' ? 'position: absolute; left: auto; top: 0px; width: 100%; ' : '' }}display: none;" >


    @foreach ($bibleTexts as $btext)

        <p class="item-comment" id="item-comment" style="display: none;" >{{ $item->comment }}</p>
        <p class="bible-text-present-ref" style="display: none;" >{{ $btext->display }}</p>

        <h1>{{ $btext->display }}</h1> 
        
        {{-- <div class="bible-text-present" style="display: none;" > --}}
            @if (isset($verses)  && count($verses))
                @php
                    if (gettype($verses)=='array')
                        $verses = $verses[0];
                @endphp
                @foreach ($verses as $verse)
                    <p class="p">
                        <sup class="v">{{ $verse->verse }}</sup> 
                        <span>{{ $verse->text }}</span>
                    </p>
                @endforeach
            @else 
                {!! $btext->text !!}
            @endif
        {{-- </div> --}}

        <!-- {!! $btext->copyright !!} -->
        <hr>

    @endforeach


</div>
