{{--
    Modal to add or edit plan notes
--}}

<div class="modal fade" id="addPlanNoteModal" data-dirty="0" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                        <div class="col-md-12 col-lg-2 lh-1 text-right">
                            <span class="text-success">{{ $note->user->name }}</span>
                            <span class="small rounded bg-grey px-1 nowrap" title="{{ $note->updated_at }}">
                                {{ $note->updated_at->formatLocalized('%a, %d %b') }}</span>
                            <small>{{ $note->updated_at->formatLocalized('%H:%M') }}</small>
                        </div>

                        <div class="col-md-12 col-lg-9">
                            <div class="card">
                                @if (Auth::user()->id == $note->user->id)
                                    <div class="card-block p-0">
                                        <small class="show-note-pencil text-muted float-right" style="text-align: left;"><i class="fa fa-pencil fa-lg"></i></small>
                                        <p class="editable-plan-note cursor-text mb-0" onclick="$('.show-note-pencil').hide()"
                                            id="plan-note-{{ $note->id }}" title="click to edit">{{ $note->text }}</p>
                                @else
                                    <div class="card-block p-0 text-muted">
                                        <p id="plan-note-{{ $note->id }}">{{ $note->text }}</p>
                                @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 col-lg-1 text-right align-bottom lh-1">
                            @if ( (Auth::user()->id == $note->user->id  &&  ! $note->read_by_leader)  ||  Auth::user()->id == $plan->leader_id )
                                <i class="fa fa-trash fa-lg cursor-pointer" title="click to delete this note"
                                    onclick="deleteUsersPlanNote({{ $note->id }}, '{{ route('api.updateNote') }}')"></i>
                            @endif
                            @if ( ! $note->read_by_leader )
                                @if (Auth::user()->id == $plan->leader_id)
                                    <span title="Mark as read" class="plan-notes-alert cursor-pointer bg-danger text-white rounded px-1 lh-2" onclick="
                                            markPlanNoteAsRead( this, {{ $note->id }}, '{{ route('api.markPlanNoteAsRead') }}' );">
                                        <i class="fa fa-check fa-lg"></i></span>
                                    <script>blink($('.plan-notes-alert'))</script>
                                @else
                                    <small>uncon&shy;firmed</small>
                                @endif
                            @elseif (Auth::user()->id != $note->user->id  &&  Auth::user()->id != $plan->leader_id)
                                <small>con&shy;firmed</small>
                            @endif
                        </div>

                        <div class="col-md-12">
                            <hr class="my-1">
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
        <button type="button" class="btn btn-primary" onclick="addNoteToPlan({{ $plan->id }})">Save changes</button>
      </div>
    </div>
  </div>
</div>


<script>
    // make the modal draggable
    $('.draggable').draggable();
</script>
