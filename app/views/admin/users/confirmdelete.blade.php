@extends('layout.main')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-danger">
            <h4>Hold on!</h4>
            <p>You are about to delete {{ $user->username }} from the site. Are you sure you want to continue deleting this user?</p>
            <p>
                {{ Form::open(array('action' => array('ADKGamers\\Webadmin\\Controllers\\Admin\\UserController@destroy', $user->id), 'method' => 'delete')) }}
                    {{ Form::submit('Yes, Delete User', ['class' => 'btn btn-danger']) }}
                    {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\UserController@show', 'No, Back To User', [$user->id], ['class' => 'btn btn-primary']) }}
                {{ Form::close() }}
            </p>
        </div>
    </div>
</div>

@stop
