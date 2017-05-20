{{--
    Modal to add or edit plan notes
--}}

<div class="modal fade" id="addPlanNoteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content draggable">

      <div class="modal-header">
          <h5>Plan Notes</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div class="modal-body">
            <div class="container-fluid">
                <div class="row">
                    @if ($plan->info)
                        {{-- show legacy plan info text --}}
                        <p>{{ $plan->info }}</p>
                    @endif
                </div>

                @foreach ($plan->notes as $note)
                    <div class="row">
                        <div class="col-md-2">
                            <p>{{ $note->user->name }}<br>
                                <span class="small rounded bg-grey px-1" title="{{ $note->updated_at }}">
                                    {{ $note->updated_at->formatLocalized('%a, %d %b') }}</span>
                            </p>
                        </div>
                        <div class="col-md-9">
                            @if (Auth::user()->id == $note->user->id)
                                <p class="editable-plan-note cursor-text" id="plan-note-{{ $note->id }}" title="click to edit">{{ $note->text }}</p>
                            @else
                                <p>{{ $note->text }}</p>
                            @endif
                        </div>
                        <div class="col-md-1 cursor-pointer">
                            @if (Auth::user()->id == $note->user->id)
                                <i class="fa fa-trash fa-lg" onclick="deleteUsersPlanNote({{ $note->id }}, '{{ route('api.updateNote') }}')"></i>
                            @endif
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                    </div>
                @endforeach

                <div class="row">
                    <div class="col-md-2">
                        <label>Add a Note:</label>
                    </div>
                    <div class="col-md-10">
                        <textarea id="textareaAddPlanNote" rows="3" class="w-100"></textarea>
                    </div>
                </div>
            </div>
      </div>


      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="addNoteToPlan()">Save changes</button>
      </div>
    </div>
  </div>
</div>


<script>
    // make the modal draggable
    $('.draggable').draggable();
</script>
