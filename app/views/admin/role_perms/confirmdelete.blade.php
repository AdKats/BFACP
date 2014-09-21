@extends('layout.main')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-danger">
            <h4>Hold on!</h4>
            <p>You are about to delete the role {{ $role->name }}. Are you sure you want to continue deleting this role?</p>
            <p>
                {{ Form::open(array('action' => array('ADKGamers\\Webadmin\\Controllers\\Admin\\RolePermsController@destroy', $role->id), 'method' => 'delete')) }}
                    {{ Form::submit('Yes, Delete Role', ['class' => 'btn btn-danger']) }}
                    {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\RolePermsController@show', 'No, Back To Role', [$role->id], ['class' => 'btn btn-primary']) }}
                {{ Form::close() }}
            </p>
        </div>
    </div>
</div>

@stop
