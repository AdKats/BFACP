@extends('layout.main')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-danger">
            <h4>Hold on!</h4>
            <p>You are about to delete {{ $user->user_name }} from the AdKats user list. This will remove all in-game admin powers and their reserved slot. Are you sure you want to continue deleting this user?</p>
            <p>
                {{ Form::open(array('action' => array('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\UserController@destroy', $user->user_id), 'method' => 'delete')) }}
                    {{ Form::submit('Yes, Delete User', ['class' => 'btn btn-danger']) }}
                    {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\UserController@show', 'No, Back To User', [$user->user_id], ['class' => 'btn btn-primary']) }}
                {{ Form::close() }}
            </p>
        </div>
    </div>
</div>

@stop
