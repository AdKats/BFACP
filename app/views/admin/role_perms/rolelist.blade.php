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

    <div class="col-xs-12">
        <p>{{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\RolePermsController@create', 'Add New Role', [], ['class' => 'btn btn-sm bg-olive']) }}</p>

        @foreach($errors->all() as $error)
            <div class="alert alert-danger">
                <i class="fa fa-ban"></i>
                <b>Error!</b> {{ $error }}
            </div>
        @endforeach

        <div class="box box-info">
            <div class="box-body table-responsive">
                <table class="table table-condensed">
                    <thead>
                        <th>Role Name</th>
                        <th>Created</th>
                        <th>Last Updated</th>
                        <th width="200px">Actions</th>
                    </thead>

                    <tbody>
                        @foreach($roles as $role)
                        <tr>
                            <td>{{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\RolePermsController@show', $role->name, [$role->id]) }}</td>
                            <td>{{ Helper::UTCToLocal($role->created_at)->format('M j, Y g:ia T') }}</td>
                            <td>{{ Helper::UTCToLocal($role->updated_at)->format('M j, Y g:ia T') }}</td>
                            <td>
                                {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\RolePermsController@edit', 'Edit Role', [$role->id], ['class' => 'btn btn-xs btn-primary']) }}
                                @if($role->id == 9 && $role->name == "Registered")
                                <button class="btn btn-xs btn-danger" disabled>Delete Role</button>
                                @else
                                {{ link_to_route('site_role_del_confirm', 'Delete Role', [$role->id], ['class' => 'btn btn-xs btn-danger']) }}
                                @endif
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
