{{-- 
        Modal to show help information 
--}}

<div class="modal fade help-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" {{ isset($id) ? 'id='.$id : '' }}>
    <div class="modal-dialog modal-md">
        <div class="modal-content bg-info">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 id="snippet-modal-title">{{ $modalTitle }}</h4>
            </div>
            <div id="snippet-modal-content" class="modal-body text-x s-center">
                {!! $modalContent !!}
            </div>
        </div>
    </div>
</div>

