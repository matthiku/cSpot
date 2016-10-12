
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Create or Update a Default Item for a specific Service")

@section('items', 'active')



@section('content')


    @include('layouts.flashing')



    <div class="container">
        @if (isset($default_item))
            {!! Form::model( $default_item, array('route' => array('default_items.update', $default_item->id), 'method' => 'put', 'id' => 'inputForm') ) !!}
        @else
            {!! Form::open(array('action' => 'Admin\DefaultItemController@store', 'id' => 'inputForm')) !!}
        @endif


        <div class="row m-b-2">

            <div class="col-sm-6 bg-info">
                <div class="pull-sm-right">
                    @if (isset($default_item))
                        <h2>Update Default Event Item</h2>
                    @else
                        <h2>Create Default Event Item</h2>
                    @endif
                </div>
            </div>

            <div class="col-sm-4 pull-sm-right">
                <span><i class="red">*</i> = mandatory field(s) &nbsp;</span>
           </div>

            <div class="col-sm-2">
            </div>

        </div>
        <hr>



        <div class="row m-b-1">
            <div class="col-sm-6">
                <div class="pull-sm-right">
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
                <div class="pull-sm-right text-sm-right">

                    <strong>Sequence number within the event:</strong> <i class="red">*</i><br>

                </div>
            </div>
            <div class="col-sm-6">

                {!! Form::number('seq_no'); !!}<br>

            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-xs-12 text-sm-right">
                <small>(Make sure the sequence number hasn't been used already in this service type - See below list of items!)</small>
            </div>
        </div>



        <div class="row m-b-1">
            <div class="col-sm-6">
                <div class="pull-sm-right">

                    {!! Form::label('text', 'Text:'); !!} <i class="red">*</i>

                </div>
            </div>
            <div class="col-sm-6">

                {!! Form::text('text'); !!}

           </div>
        </div>



        <div class="row m-b-1">
            <div class="col-sm-6">
                <div class="pull-sm-right">
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
                            <label class="m-r-2" style="vertical-align: top;">Default<br>Image<br>for this<br>item:</label>
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

                <input type="hidden" id="file_id" name="file_id">

            </div>
        </div>


        <hr>
        <div class="row m-b-1">
            <div class="col-sm-6">
                <div class="pull-sm-right">


                    @if (isset($default_item))
                        {!! Form::submit('&#10003; Update'); !!}
                    @else
                        {!! Form::submit('&#10003; Submit'); !!}
                    @endif

                </div>
            </div>
            <div class="col-sm-6">

                @if (isset($default_item))
                    <a class="btn btn-danger btn-sm pull-xs-right"  type="button" 
                            href="{{ url('admin/default_items/'. $default_item->id) }}/delete">
                        <i class="fa fa-trash" > </i> &nbsp; Delete
                    </a>
                @endif

                <a href="{{ url()->previous() }}">{!! Form::button('&#10008; Cancel'); !!}</a>

           </div>
        </div>


        {!! Form::close() !!}


        <hr>
        <div class="row">
            <div class="col-xs-12 m-x-auto">

                @if (isset($default_items))
                    <h5 class="text-muted">Currently defined Default Items for this type of event:</h5 class="text-muted">
                    <table class="table table-sm table-striped m-x-auto">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Seq.No.</th>
                                <th>Text</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($default_items as $item)
                                <tr class="link show-all-default-items show-existing-{{ $item->type_id }}" style="display: none;"
                                    onclick="location.href ='{{ url('admin/default_items/' . $item->id) }}/edit'">
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->seq_no }}</td>
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
            $('.show-all-default-items').hide();            
            $('.show-existing-'+type).show();            
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