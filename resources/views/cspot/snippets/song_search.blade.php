

{!! Form::label('search', 'enter song number, title or author or parts thereof:') !!}
{!! Form::text('search') !!}
<input type="submit" name="searchBtn" value="Search" />
@if ($errors->has('search'))
    <br><span class="help-block">
        <strong>{{ $errors->first('search') }}</strong>
    </span>
@endif

