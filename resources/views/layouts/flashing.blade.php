

@if (Session::has('status'))
    <div class="alert alert-success alert-dismissable fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        {{ Session::get('status') }}
    </div>
@endif


@if ($errors)
    @foreach( $errors->all() as $error )
        <div class="alert alert-danger alert-dismissable fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        <strong>Oh snap!</strong> {{ $error }}
        </div>
    @endforeach
@endif

