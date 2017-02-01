{{-- 
        Modal to show help information 
--}}

<div class="modal fade help-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" {{ isset($id) ? 'id='.$id : '' }}>
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-info">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 id="snippet-modal-title">{{ isset($modalTitle) ? $modalTitle : '' }}</h4>
            </div>
            <div id="snippet-modal-content" class="modal-body text-center">
                {!! isset($modalContent) ? $modalContent : '' !!}
            </div>
        </div>
    </div>
</div>

