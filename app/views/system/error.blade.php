@extends('layout.main')

@section('content')
<div class="error-page">
    <h2 class="headline text-red"><?php echo $code; ?></h2>
    <div class="error-content">
        <h3><i class="fa fa-warning text-red"></i> Oops! Something went wrong.</h3>

        <p>A fatal error occured and application could not continue. Please report this incident to the site administrator and give them the following error message.</p>

        <samp><?php echo $exception->getMessage(); ?></samp>
    </div>
</div>
@stop
