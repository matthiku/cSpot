
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Create or Update a Default Item for a specific Service")

@section('items', 'active')



@section('content')


    @include('layouts.flashing')



    <div class="container">
        @if (isset($default_item))
            {!! Form::model( $default_item, array('route' => array('default_items.update', $default_item->id), 'method' => 'put', 'id' => 'inputForm', 'oninput' => 'enableSubmitButton()') ) !!}
        @else
            {!! Form::open(array('action' => 'Admin\DefaultItemController@store', 'id' => 'inputForm', 'oninput' => 'enableSubmitButton()')) !!}
        @endif


        <div class="row">

            <div class="col-sm-6 bg-info">
                <div class="float-sm-right">
                    @if (isset($default_item))
                        <h2>Update Default Event Item</h2>
                    @else
                        <h2>Create Default Event Item</h2>
                    @endif
                </div>
            </div>

            <div class="col-sm-4">
                <span><i class="red">*</i> = mandatory field(s) &nbsp;</span>
           </div>

            <div class="col-sm-2">
            </div>

        </div>

        @if (!isset($default_item))
            <div class="row">
                <div class="col-xs-12 small mb-0 text-xs-center">
                    <p>"<strong>Default Items</strong>" are items in a Service Plan or Event that are always (or usually) the same.<br>
                    Instead of having to create them each time you create a new event, have them defined here and assign them to a certain event type.<br>
                    The next time this event type will be created, the Default Items for this type will be added to this new event automatically.</p>
                </div>
            </div>
        @endif



        <div class="row mt-2 mb-1 pt-1 bg-muted">
        
            <div class="col-sm-6">
                <div class="float-sm-right">
                    <label>Event/Service Type</label> <i class="red">*</i>
                </div>
            </div>
            <div class="col-sm-6">

                <select name="type_id" id="type_id" class="c-select" onchange="showExistingItems();">
                  <option selected>Select ...</option>
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

           </div>
        </div>


        <div class="row">
            <div class="col-sm-6">
                <div class="float-sm-right text-sm-right">

                    <strong>Sequence number within the event:</strong> <i class="red">*</i><br>

                </div>
            </div>
            <div class="col-sm-6">

                {!! Form::text('seq_no'); !!}<br>

            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-xs-12 text-sm-right">
                <p class="float-sm-right" style="max-width: 23rem; line-height: 1;">
                    <small>(Make sure the sequence number hasn't been used already in this service type - See list of existing items below!)</small></p>
            </div>
        </div>



        <div class="row mb-1 pt-1 bg-muted">
            <div class="col-sm-6">
                <div class="float-sm-right">

                    {!! Form::label('text', 'Text:'); !!} <i class="red">*</i>

                </div>
            </div>
            <div class="col-sm-6">

                {!! Form::text('text'); !!}

           </div>
        </div>


        <div class="row">
            <div class="col-sm-6">
                <div class="float-sm-right">

                    {!! Form::label('text', 'Item always visible for all?'); !!} 

                </div>
            </div>
            <div class="col-sm-6">

                <label class="custom-control custom-radio">
                    <input id="radio1" name="forLeadersEyesOnly" type="radio" class="custom-control-input"
                        {{ isset($default_item) ? ($default_item->forLeadersEyesOnly ? '' : 'checked="checked"') : '' }}>
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description"><i class="fa fa-eye"></i> visible for everyone</span>
                </label>
                <label class="custom-control custom-radio">
                    <input id="radio2" name="forLeadersEyesOnly" type="radio" class="custom-control-input"
                        {{ isset($default_item) ? ($default_item->forLeadersEyesOnly ? 'checked="checked"' : '') : '' }}>
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description"><i class="fa fa-eye-slash"></i> for Leader's eyes only!</span>
                </label>

           </div>
        </div>


        <div class="row mb-1 py-1 bg-muted">
            <div class="col-sm-6">
                <div class="float-sm-right">

                    {!! Form::label('text', 'Show item text as title in the presentation?'); !!} 

                </div>
            </div>
            <div class="col-sm-6">

                <div class="btn btn-secondary btn-sm link float-xs-left mr-1" onclick="toggleBooleanButton(this);">
                    {!! Form::hidden('showItemText', '0') !!}
                    {!! Form::hidden('showItemText') !!}
                    <span>{!! isset($default_item) ? $default_item->showItemText ? '&#10004;' : '&#10008;' : '&#10008;' !!}</span>
                </div>    

           </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="float-sm-right">

                    {!! Form::label('text', 'use this item to show the Announcements?'); !!} 

                </div>
            </div>
            <div class="col-sm-6">

                <div class="btn btn-secondary btn-sm link float-xs-left mr-1" onclick="toggleBooleanButton(this);">
                    {!! Form::hidden('showAnnouncements', '0') !!}
                    {!! Form::hidden('showAnnouncements') !!}
                    <span>{!! isset($default_item) ? $default_item->showAnnouncements ? '&#10004;' : '&#10008;' : '&#10008;' !!}</span>
                </div>    

           </div>
        </div>



        <div class="row mb-1 bg-muted">
            <div class="col-sm-6">
                <div class="float-sm-right">
                    @if (isset($default_item))
                        <a href="#" onclick="$('.add-files-card').toggle();$('.show-one-file-figure').toggle();">Set/Change default image for this item</a>
                    @else
                        <a href="#" onclick="$('.add-files-card').toggle();$('.show-one-file-figure').toggle();">Add default image for this item</a>
                    @endif                    
                </div>
            </div>
            <div class="col-sm-6">
                        
                @if (isset($default_item))
                    <?php 
                        // set value for included Blade view
                        $file = $default_item->file; 
                    ?>
                    
                    @if ($file)
                        <div class="show-one-file-figure">                    
                            <label class="mr-2" style="vertical-align: top;">Default<br>Image<br>for this<br>item:
                                <br>
                                <span>
                                    <a class="small" href="#" title="Unlink this file from this item"
                                        onclick="$('#file_id').val('');$('.show-one-file-figure').hide();$('.figure').remove();">
                                    <i class="fa fa-unlink"></i> Unlink</a>
                                </span>
                            </label>
                            @include ('cspot.snippets.show_files')
                        </div>

                    @endif

                @endif

           </div>
        </div>


        <div class="row">
            <div class="col-xs-12">
                <?php 
                    // set modus for File Selection code
                    $modal = 'default_items'; 
                ?>
                
                <div class="add-files-card" style="display: none; max-width: 55rem;">            
                    @include ('cspot.snippets.add_files')
                </div>

                <input type="hidden" id="file_id" name="file_id"
                    {{ isset($default_item) ? 'value='.$default_item->file_id : '' }}>

            </div>
        </div>


        <hr>
        <div class="row mb-1">
            <div class="col-sm-6">
                <div class="float-sm-right">

                    @if (isset($default_item))
                        {!! Form::submit('Update', ['class'=>'btn btn-outline-success submit-button disabled']); !!}
                    @else
                        {!! Form::submit('Submit', ['class'=>'btn btn-outline-success submit-button disabled']); !!}
                    @endif

                </div>
            </div>
            <div class="col-sm-6">

                @if (isset($default_item))
                    <a class="btn btn-danger float-xs-right"
                            href="{{ url('admin/default_items/'. $default_item->id) }}/delete">
                        <i class="fa fa-trash" > </i> &nbsp; Delete
                    </a>
                @endif

                <a href="{{ url()->previous() }}">{!! Form::button('&#10008; Cancel', ['class'=>'btn btn-secondary cancel-button']); !!}</a>

           </div>
        </div>


        {!! Form::close() !!}


        <hr>
        <div class="row bg-muted">
            <div class="col-xs-12">

                @if (isset($default_items))

                    <h5 class="text-muted text-sm-center">All currently defined <i>Default Items</i> for this type of event:</h5>

                    <table class="table table-sm table-striped m-x-auto" style="width: initial;">
                        <thead>
                            <tr class="show-header" style="display: none;">
                                <th>ID</th>
                                <th>Seq.No.</th>
                                <th>Text</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($default_items as $item)
                                <tr class="link show-all-default-items show-existing-{{ $item->type_id }}" style="display: none;"
                                    onclick="location.href ='{{ url('admin/default_items/' . $item->id) }}/edit'">
                                    <td class="center">{{ $item->id }}</td>
                                    <td class="center red">{{ $item->seq_no }}</td>
                                    <td>{{ $item->text }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

            </div>
        </div>



    </div>


    <script>

        // indicate item type to spa.utilities.js
        cSpot.item = 'default_items';


        // show existing items for this type in the table below the form
        function showExistingItems() {
            var type = $('#type_id').val();
            if ( isNaN(type) ) return;
            $('.show-all-default-items').hide();            
            $('.show-existing-'+type).show();            
            $('.show-header').show();            
        }

        // set value of selector box depending on where the user    
        // came from and whether a filter was set in the previous list

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
                    var type_id;

                    if (p[1]=='type' && q[0]=='filtervalue') 
                        type_id = q[1];

                    if (q[1]=='type' && p[0]=='filtervalue')
                        type_id = p[1];

                    $('#type_id').val(type_id);

                    // set focus on SeqNo field
                    document.forms.inputForm.seq_no.focus()
                }
            }

            // set size of input field for item text to max
            $('[name="text"]').addClass('w-100')

            showExistingItems();

            // hide the title for the Add Files card
            $('.card-title').parent().hide()
        });

    </script>

    
@stop