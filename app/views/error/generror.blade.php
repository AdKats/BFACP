@extends('layout.main')

@section('content')
<div class="error-page">
    <h2 class="headline">{{ $code }}</h2>
    <div class="error-content">
        <h3><i class="fa fa-warning text-yellow"></i> {{ $errmsg }}</h3>
        <p>
            @if($errdescription === FALSE)
                {{ $errors->first('player') }}
            @elseif(is_string($errdescription))
            {{ $errdescription }}
            @endif
        </p>

    </div>
</div>

@stop
