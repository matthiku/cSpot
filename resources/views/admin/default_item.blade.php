
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
            <select name="type_id" class="c-select">
              <option selected>Select Service</option>
              @foreach ($types as $type)
                <option value="{{ $type->id }}"
                    @if ($type->id == $default_item->type_id)
                        selected="selected"
                    @endif
                    >
                    {{ $type->name }}
                </option>
              @endforeach
            </select>
        </p>

        <p>{!! Form::label('seq_no', 'Sequence number within the Service'); !!}<br>
           {!! Form::number('seq_no'); !!}</p>

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


    <a href="{{ url('admin/default_items') }}">{!! Form::button('Cancel'); !!}</a></p>
    {!! Form::close() !!}


    <script type="text/javascript">document.forms.inputForm.type_id.focus()</script>

    
@stop