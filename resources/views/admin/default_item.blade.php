
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Create or Update a Default Item for a specific Service")

@section('items', 'active')



@section('content')


    @include('layouts.flashing')


    @if (isset($default_item))
        <h2>Update Default Item</h2>
        {!! Form::model( $default_item, array('route' => array('admin.default_items.update', $default_item->id), 'method' => 'put', 'id' => 'inputForm') ) !!}
    @else
        <h2>Create Default Item</h2>
        {!! Form::open(array('action' => 'Admin\DefaultItemController@store', 'id' => 'inputForm')) !!}
    @endif


        <p>
            <select name="type_id" id="type_id" class="c-select">
              <option selected>Select Service Type</option>
              @foreach ($types as $type)
                <option value="{{ $type->id }}"
                    @if (isset($default_item) && $type->id == $default_item->type_id)
                        selected="selected"
                    @endif
                    >
                    {{ $type->name }}
                </option>
              @endforeach
            </select>
        </p>


        <p>{!! Form::label('seq_no', 'Sequence number within the Service'); !!}<br>
           {!! Form::number('seq_no'); !!}<br>
            <small>Make sure the sequence number hasn't been used already in this service type!</small>
        </p>


        <p>{!! Form::label('text', 'Text'); !!}<br>
           {!! Form::text('text'); !!}</p>



        @if (isset($default_item))
            <p>{!! Form::submit('Update'); !!}</p>
            <hr>
            <a class="btn btn-danger btn-sm"  default_item="button" href="{{ url('admin/default_items/'. $default_item->id) }}/delete">
                <i class="fa fa-trash" > </i> &nbsp; Delete
            </a>
        @else
            <p>{!! Form::submit('Submit'); !!}
        @endif


        <a href="{{ url()->previous() }}">{!! Form::button('Cancel'); !!}</a></p>

    {!! Form::close() !!}


    <script>

        document.forms.inputForm.type_id.focus()

        var prev_url = "{!! url()->previous() !!}";

        $(document).ready( function() {
            var old_url = parseURLstring(prev_url);

            if (old_url.search.length > 0) {
                var s = old_url.search.split('?');
                var t = s[1].split('&');

                if (t.length>1) {
                    var p = t[0].split('=');
                    var q = t[1].split('=');

                    if (p[1]=='type' && q[0]=='filtervalue') 
                        $('#type_id').val(q[1]);

                    if (q[1]=='type' && p[0]=='filtervalue')
                        $('#type_id').val(p[1]);
                    document.forms.inputForm.seq_no.focus()
                }
            }
            $('[name="text"]').css('min-width', '20rem')
        });

    </script>

    
@stop