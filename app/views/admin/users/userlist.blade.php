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
        <div class="box box-info">
            <div class="box-body table-responsive">
                <table class="table table-condensed">
                    <thead>
                        <th>Username</th>
                        <th>Email</th>
                        <th>User Group</th>
                        <th>Active</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </thead>

                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ link_to_action('ADKGamers\\Webadmin\\Controllers\\AccountController@showUserProfile', $user->username, [$user->id]) }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->name }}</td>
                            <td>
                                @if($user->confirmed)
                                <span class="label label-success">Yes</span>
                                @else
                                <span class="label label-danger">No</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->timezone($user_tz)->format('M j, Y g:ia T') }}</td>
                            <td>
                                {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\UserController@edit', 'Edit User', [$user->id], ['class' => 'btn btn-xs btn-primary']) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@stop

