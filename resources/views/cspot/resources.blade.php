
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', 'Service Plan Management')

@section('plans', 'active')


@section('content')


    <?php 
        // create shortcut to a complex question....
        $userIsAuthorized = Auth::user()->isEditor() || Auth::user()->ownsPlan($plan->id);
    ?>

    @include('layouts.flashing')

    @if (  $plan->resources->count() > 0  &&  $userIsAuthorized  )  
        <button onclick="$('#new-resource-form').toggle();" class="pull-xs-right m-l-1">Add Resource</button>
    @endif
    

    <h4>Resources for 
        <a href="{{ url('cspot/plans/'.$plan->id) }}">
            "{{ $plan->type->name }}" on 
            <span class="hidden-md-down">{{ $plan->date->formatLocalized('%A, %d %B %Y') }}</span>
            <span class="hidden-lg-up"  >{{ $plan->date->formatLocalized('%a, %d %B')    }}</span>
        </a>
    </h4>


    <!-- 
        show the current resource members and their roles 
    -->
    <table class="table table-striped table-bordered 
                @if(count($plan->resources)>15)
                 table-sm
                @endif
                 ">
        <thead class="thead-default">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th class="hidden-sm-down">Type</th>
                <th class="hidden-md-down">Details</th>
                <th class="hidden-xs-down">Comment</th>
                @if( $userIsAuthorized )
                    <th>Action</th>
                @endif
            </tr>
        </thead>

        <tbody>
        @foreach( $plan->resources as $resource )
            <tr>

                <td scope="row">
                    {{ $resource->id }}
                </td>
                <!-- row NAME -->
                <td scope="row">
                    {{ $resource->name }}
                </td>
                <td class="hidden-sm-down" scope="row">
                    {{ $resource->type }}
                </td>
                <td class="hidden-md-down" scope="row">
                    {{ $resource->details }}
                </td>
                <td class="hidden-xs-down" scope="row">
                    <span id="comment-resource-id-{{ $resource->pivot->id }}" class="editable-resource comment-textcontent hover-show">{{ $resource->pivot->comment }}</span>

                    {{-- show editing icon only when comment is not empty and when hovering over it 
                    @if ($resource->pivot->comment)
                        <span class="hover-only fa fa-pencil text-muted"></span>
                    @endif--}}
                </td>

                <!-- row ACTIONS -->
                @if( $userIsAuthorized )
                <td class="nowrap">
                        {{-- <a class="btn btn-outline-primary btn-sm" title="Edit" 
                            onclick="$('#show-spinner').modal({keyboard: false});" 
                            href='{{ url('cspot/plans/'.$plan->id.'/resource/'.$resource->id) }}/edit'><i class="fa fa-pencil"></i></a> --}}
                        <a class="btn btn-danger btn-sm" title="Delete!" 
                            onclick="$('#show-spinner').modal({keyboard: false});" 
                            href='{{ url('cspot/plans/'.$plan->id.'/resource/'.$resource->id) }}/delete'><i class="fa fa-trash"></i></a>
                </td>
                @endif

            </tr>

        @endforeach
        </tbody>

    </table>



    <!-- 
        show form to enter a new resource 
    -->
    @if ( $plan->resources->count() == 0 )
        <h5>Add a new resource to this plan:</h5>
    @endif

    <form id="new-resource-form" method="POST" 
        @if ( $plan->resources->count() > 0 )
            style="display: none"
        @endif
        >

        <input type="hidden" name="_method" value="POST">
        {{ csrf_field() }}

        <label class="form-control-label">Select a Resource:</label>
        <select name="resource_id" class="form-control text-help c-select">
            <option selected>
                Select ...
            </option>
            @foreach ($resources as $resource)
                <option 
                    @if(  ''<>old('resource_id') && $resource->id==old('resource_id') )  
                            selected
                    @endif
                    value="{{ $resource->id }}">{{ $resource->name }}
                </option>
            @endforeach
        </select>
        @if ($errors->has('resource_id'))
            <br><span class="help-block">
                <strong>{{ $errors->first('resource_id') }}</strong>
            </span>
        @endif

        <br>
        <label class="form-control-label" id="comment-input">
            Add a comment (optional):
            <input type="text" name="comment">
        </label>
        
        <div class="form-control-label">
            <button type="submit" id="submit-button">Submit</button>
        </div>

    </form>


@stop