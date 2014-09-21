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

    @if(Session::has('errors'))
        <div class="col-md-12">
            @foreach($errors->all() as $error)
            <div class="alert alert-danger alert-dismissable">
                <i class="fa fa-ban"></i>
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <b>Error!</b> {{ $error }}
            </div>
            @endforeach
        </div>
    @endif

    <div class="col-md-6">

        <p>The {{ $role->role_name }} role has access to these commands:</p>

        <div class="box box-info">
            <div class="box-body table-responsive">
                <table class="table table-condensed">
                    <thead>
                        <th>Name</th>
                        <th>Command</th>
                    </thead>

                    <tbody>
                        @foreach($role->permissions as $perm)
                        <tr>
                            <td>{{ $perm->command->command_name }}</td>
                            <td>{{ $perm->command->command_text }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <p>
            {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\RolePermsController@edit', 'Edit Role', [$role->role_id], ['class' => 'btn btn-primary']) }}
            {{ link_to_route('adkat_role_del_confirm', 'Delete Role', [$role->role_id], ['class' => 'btn btn-danger']) }}
            {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\RolePermsController@index', 'Back To Rolelist', [], ['class' => 'btn bg-olive']) }}
        </p>
    </div>

    <div class="col-md-6">
        <p>Users assigned to this role:</p>

        <div class="box box-info">
            <div class="box-body table-responsive">
                <table class="table table-condensed">
                    <thead>
                        <th>Username</th>
                        <th>Notes</th>
                    </thead>

                    <tbody>
                        @foreach($role->users as $user)
                        <tr>
                            <td>{{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\UserController@show', $user->user_name, [$user->user_id]) }}</td>
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
