@section('content')

<div class="row">

    @if(Session::has('message'))
        <div class="col-md-12">
            <div class="alert alert-success alert-dismissable">
                <i class="fa fa-check"></i>
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <b>Success!</b> {{ Session::get('message') }}
            </div>
        </div>
    @endif

    <div class="col-md-12">
        <p>{{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\UserController@create', 'Add New User', [], ['class' => 'btn btn-sm bg-olive']) }}</p>
        <div class="box box-info">
            <div class="box-body table-responsive">
                <table class="table table-condensed">
                    <thead>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Expires</th>
                        <th>Notes</th>
                    </thead>

                    <tbody>
                        @foreach($userlist as $user)
                        <tr>
                            <td>{{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\UserController@show', $user->user_name, [$user->user_id]) }}</td>
                            <td>{{ $user->role_name }}</td>
                            <td>{{ $user->user_expiration->timezone($user_tz)->format('M j, Y g:ia T') }}</td>
                            <td>{{ $user->user_notes }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@stop

