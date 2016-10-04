<!-- Modal -->
<div class="modal fade" id="addPlanNoteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content draggable">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
          <label>Enter your note:</label>
          <textarea id="textareaAddPlanNote" rows="4"></textarea>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="addNoteToPlan()">Save changes</button>
      </div>
    </div>
  </div>
</div>