
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Create or Update a User File Category")

@section('setup', 'active')



@section('content')


    @include('layouts.flashing')


    @if (isset($file_category))
        <h2>Update File Category</h2>
        {!! Form::model( $file_category, array('route' => array('admin.file_categories.update', $file_category->id), 'method' => 'put', 'id' => 'inputForm') ) !!}
    @else
        <h2>Create new File Category</h2>
        {!! Form::open(array('action' => 'Admin\FileCategoryController@store', 'id' => 'inputForm') ) !!}
    @endif
        <p>{!! Form::label('name', 'File Category Name'); !!}<br>
           {!! Form::text('name'); !!}</p>

    @if (isset($file_category))
        <p>{!! Form::submit('Update'); !!}</p>
        <hr>
        <!--a class="btn btn-danger btn-sm"  instrument="button" href="{{ url('admin/file_categories/'.$file_category->id) }}/delete">
            <i class="fa fa-trash" > </i> &nbsp; Delete
        </a-->
    @else
        <p>{!! Form::submit('Submit'); !!}
    @endif

    <script type="text/javascript">document.forms.inputForm.name.focus()</script>

    <a href="{{ url('admin/file_categories/') }}">{!! Form::button('Cancel'); !!}</a></p>
    {!! Form::close() !!}
    
@stop