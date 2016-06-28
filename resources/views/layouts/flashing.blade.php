
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

<div id="show-spinner" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content center p-b-2 p-t-2">
        <h2><i class="fa fa-spin fa-spinner fa-4"></i> wait ...</h2>
    </div>
  </div>
</div>



@if (session()->has('message') && ! session('message')=='')
    <div id="myMsgModal" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="alert alert-info center" role="alert">
                {{ session('message') }}
            </div>
        </div>
      </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#myMsgModal').modal('show');
            delayedCloseFlashingModals($('#myMsgModal'));
        });        
    </script>
@endif


@if (session()->has('error'))
    <div id="myErrorModal" class="modal fade">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">Oh snap!</h4>
          </div>
          <div class="modal-body">
               <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <script>
        $(document).ready(function() {
            $('#myErrorModal').modal('show');
        });        
    </script>
@endif



@if (Session::has('status') && ! Session::get('status')=='')
    <div id="myMsgModal" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="alert alert-info center" role="alert">
                Status: {{ Session::get('status') }}
            </div>
        </div>
      </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#myMsgModal').modal('show');
            delayedCloseFlashingModals($('#myMsgModal'));
        });        
    </script>
@endif


@if (count($errors))
    <div id="myErrorModal" class="modal fade">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">Oh no!</h4>
          </div>
          <div class="modal-body">
            @foreach( $errors->all() as $error )
                <div class="alert alert-danger" role="alert">{{ $error }}</div>
            @endforeach
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <script>
        $(document).ready(function() {
            $('#myErrorModal').modal('show');
        });        
    </script>
@endif

