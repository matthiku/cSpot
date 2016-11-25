
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Create or Update a User File Category")

@section('setup', 'active')



@section('content')


    @include('layouts.flashing')


    @if (isset($file_category))
        <h2>Update File Category</h2>
        {!! Form::model( $file_category, array('route' => array('file_categories.update', $file_category->id), 'method' => 'put', 'id' => 'inputForm', 'oninput' => 'enableSubmitButton()') ) !!}
    @else
        <h2>Create new File Category</h2>
        {!! Form::open(array('action' => 'Admin\FileCategoryController@store', 'id' => 'inputForm', 'oninput' => 'enableSubmitButton()') ) !!}
    @endif
        <p>{!! Form::label('name', 'File Category Name'); !!} <i class="red">*</i><br>
           {!! Form::text('name'); !!}</p>

    @if (isset($file_category))
        <p>{!! Form::submit('Update', ['class'=>'btn btn-outline-success submit-button disabled']); !!}</p>
        <hr>
        <!--a class="btn btn-danger btn-sm"  instrument="button" href="{{ url('admin/file_categories/'.$file_category->id) }}/delete">
            <i class="fa fa-trash" > </i> &nbsp; Delete
        </a-->
    @else
        <p>{!! Form::submit('Submit', ['class'=>'btn btn-outline-success submit-button disabled']); !!}
    @endif

    <script type="text/javascript">document.forms.inputForm.name.focus()</script>

    <a href="{{ url('admin/file_categories/') }}">{!! Form::button('Cancel', ['class'=>'btn btn-secondary cancel-button']); !!}</a></p>
    {!! Form::close() !!}
    
    <span><i class="red">*</i> = mandatory field(s) &nbsp;</span>
    
@stop