

<!-- 
    Modal for creating a new message 
-->

<div class="modal fade" id="createMessage" tabindex="-1" role="dialog" aria-labelledby="createMessageLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="createMessageLabel">Report an issue or other feedback</h4>
            </div>
            {!! Form::open(['route' => 'messages.store']) !!}

                <div class="modal-body">
                    <!-- Subject Form Input -->
                    <div class="form-group">
                        {!! Form::label('subject', 'Subject', ['class' => 'control-label']) !!}
                        {!! Form::text('subject', 'Feedback on page '.url()->full(), ['class' => 'form-control' ]) !!}
                    </div>

                    <!-- Message Form Input -->
                    <div class="form-group">
                        {!! Form::label('message', 'Message', ['class' => 'control-label']) !!}
                        {!! Form::textarea('message', null, ['class' => 'form-control', 'id' => 'feedbackMessage']) !!}
                    </div>
                    
                    @foreach ($administrators as $admin)
                        <input type="hidden" name="recipients[]" value="{{ $admin }}">
                    @endforeach
                </div>


                <div class="modal-footer">

                    <!-- Submit Form Input -->
                    <div class="form-group">
                        <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">Cancel</button>
                        {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
                    </div>

                </div>
                
            {!! Form::close() !!} 


        </div>
    </div>
</div>
