
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Create or Update Plan Item")

@section('plan', 'active')



@section('content')

    @include('layouts.flashing')


    <div class="row">
        <div class="col-md-6">
            <h2>Select a Song for item {{ $seq_no }}.0 </h2>
            <h5>of plan for {{ $plan->type->name }} on {{ $plan->date->formatLocalized('%d-%b-%Y') }}</h5>
        </div>
    </div>
    

    <div class="row">
        <div class="col-lg-6">

            <?php $counter=0; ?>
            @foreach ($songs as $song)
                <?php $counter+=1; ?>
                <a 
                    @if ($item_id)
                        href='{{ url('cspot/plans/'.$plan->id) }}/items/update/item/{{$item_id }}/song/{{ $song->id }}' 
                    @else
                        href='{{ url('cspot/plans/'.$plan->id) }}/items/store/seq_no/{{$seq_no }}/song/{{ $song->id }}/{{$moreItems}}' 
                    @endif
                    class="btn btn-primary-outline btn-sm btn-select" role="button"
                    title="{{ substr($song->lyrics, 0, 250).' ...' }}" data-toggle="tooltip" 
                    @if ($counter<6)
                        data-placement="bottom"
                    @endif
                >
                        {{ $song->book_ref ? $song->book_ref.',' : '' }}
                        {{ $song->title }}
                        {{ $song->title_2 ? '('. $song->title_2 .')' : '' }}
                    <br>
                    <small>
                        @if ( $song->items->count() )
                            (used {{ $song->items->count() }} times, last on 
                            {{ $song->lastPlanUsingThisSong()->date->formatLocalized('%d-%b-%Y') }})
                        @else
                            (never used)
                        @endif
                    </small>

                </a>
                <br>
            @endforeach

            <a href='#' onclick='window.history.back();' 
                class="btn btn-secondary btn-select" role="button">Back ...</a>


        </div>
    </div>


@stop