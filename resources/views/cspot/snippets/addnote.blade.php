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
                        <p>{{ $plan->info }}</p>
                    @endif
                    @foreach ($plan->notes as $note)
                        <div class="col-md-2">
                            <p>{{ $note->user->name }}<br>
                                <span class="small">{{ $note->updated_at->formatLocalized('%a, %d %b') }}</span>
                            </p>
                        </div>
                        <div class="col-md-9">
                            @if (Auth::user()->id == $note->user->id)
                                <p class="editable" title="click to edit">{{ $note->text }}</p>
                            @else
                                <p>{{ $note->text }}</p>
                            @endif
                        </div>
                        <div class="col-md-1">
                            @if (Auth::user()->id == $note->user->id)
                                <i class="fa fa-pencil fa-lg"></i> <i class="fa fa-trash fa-lg"></i>
                            @endif
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                    @endforeach
                    <div class="col-md-2">
                        <label>Add a note:</label>
                    </div>
                    <div class="col-md-10">
                        <textarea id="textareaAddPlanNote" rows="4" class="w-100"></textarea>
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
