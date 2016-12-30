
<div class="mb-3">
    <div class="text-onsong" id="onsongs" style="column-count: 1;">
        @foreach ( $onSongChords as $onsong )
            <div class="keeptogether">                            
                @if ($onsong->song_part->code!='m')
                    <div class="chords-header pl-3 text-white {{ is_numeric($onsong->song_part->code) ? 'bg-success' : 'bg-info' }}" 
                          id="song-part-{{ $onsong->song_part->code }}">{{ $onsong->song_part->name }}:</div>
                @endif

                <div class="chords-part{{ $onsong->song_part->code!='m' ? ' lh-1 show-onsong-text' : ' lh-1h white-space-pre-wrap bigger red' }}">{{ $onsong->text }}</div>
            </div>
        @endforeach
    </div>
</div>
