
@extends('layout.main')

@section('content')
<div class="error-page">
    <h2 class="headline text-red">&nbsp;403</h2>
    <div class="error-content">
        <h3><i class="fa fa-warning text-red"></i> Access Forbidden</h3>

        <p>You do not have the correct permissions to view this page.</p>
        <p>{!! link_to_route('home', 'Return to dashboard', [], ['target' => '_self']) !!}</p>
    </div>
</div>
@stop
