@if (session()->has('message'))
    <div class="alert alert-info">{{ session('message') }}</div>
@endif

@if (session()->has('error'))
    <div class="alert alert-warning">{{ session('error') }}</div>
@endif
