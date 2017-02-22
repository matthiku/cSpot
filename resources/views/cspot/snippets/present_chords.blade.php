
<div class="mb-3">
    <div class="text-onsong" id="onsongs" style="column-count: 1;">

        @php
            // in order for not to show a song part twice, 
            // we want to keep track of what has already been shown
            $shown = [];
            $repeat = false;
            $then = false;
        @endphp


        @foreach ( $onSongChords as $onsong )

            @php
                if (in_array($onsong->song_part->code, $shown)) {
                    if ($repeat) 
                        $then = true;
                    $repeat = true;
                }
                else {
                    array_push($shown, $onsong->song_part->code);
                    $repeat = false;
                    $then = false;
                }
            @endphp

            @if ($onsong->song_part->code!='m')
                @if ($repeat)
                    <span class="ml-2 bg-faded text-info">{{ $then ? 'Then play' : 'Repeat' }} 
                    <span class="font-weight-bold text-white rounded px-1 {{ 
                        is_numeric($onsong->song_part->code) ? 'bg-success' : 'bg-info' }}">{{ $onsong->song_part->name }}</span>! </span>
                @else
                    <div class="keeptogether"> 
                        <div class="chords-header pl-3 text-white {{ is_numeric($onsong->song_part->code) ? 'bg-success' : 'bg-info' }}" 
                            id="song-part-{{ $onsong->song_part->code }}">{{ $onsong->song_part->name }}:
                        </div>
                @endif
            @endif

            @if (!$repeat)
                <div class="chords-part{{ 
                    $onsong->song_part->code!='m' ? ' lh-1 show-onsong-text' : ' lh-1h white-space-pre-wrap bigger red' }}">{{ 
                        $onsong->text }}</div>
                @if ($onsong->song_part->code!='m')
                    </div>
                @endif
            @endif

        @endforeach


    </div>
</div>
