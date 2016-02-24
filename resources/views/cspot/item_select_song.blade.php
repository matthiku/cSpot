
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Create or Update Plan Item")

@section('plan', 'active')



@section('content')


    @include('layouts.flashing')

    <div class="row">
        <div class="col-md-6">
            <h2>Select a Song</h2>
        </div>
    </div>


    <hr>

    <script>
        function submitForm() {
            $('#btnSubmit').click();
        }
    </script>

    <div class="row">
        <div class="col-lg-6">


            <div>

                @foreach ($songs as $song)
                    <a 
                        @if ($item_id)
                            href='{{ url('cspot/plans/'.$plan_id) }}/items/update/item/{{$item_id }}/song/{{ $song->id }}' 
                        @else
                            href='{{ url('cspot/plans/'.$plan_id) }}/items/store/seq_no/{{$seq_no }}/song/{{ $song->id }}/{{$moreItems}}' 
                        @endif
                        class="btn btn-primary-outline" role="button"
                        title="{{ substr($song->lyrics, 0, 250).' ...' }}" data-toggle="tooltip" data-placement="bottom">
                        {{ $song->book_ref ? $song->book_ref.',' : '' }}
                        {{ $song->title }}
                        {{ $song->title_2 ? '('. $song->title_2 .')' : '' }}
                        <br>
                        <small>
                            @if ( $song->items->count() )
                                (used {{ $song->items->count() }} times, last on 
                                {{ $song->lastPlanUsingThisSong()->date->formatLocalized('%d-%b-%y') }})
                            @else
                                (never used)
                            @endif
                        </small>

                    </a>
                    <br>
                @endforeach

            </div>

            <br>
            <br>


            <div class="bg-grey center">
                    <a href='#' onclick='window.history.back();' 
                        class="btn btn-primary-outline" role="button">Back ...</a>

            </div>

        </div>
    </div>


    
@stop