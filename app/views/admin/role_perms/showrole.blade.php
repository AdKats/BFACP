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

        <p>The {{ $role->name }} role has access to these commands:</p>

        <div class="box box-info">
            <div class="box-body table-responsive">
                <table class="table table-condensed">
                    <tbody>
                        @foreach($role->permissions() as $perm)
                        <tr>
                            <td>
                                @if(starts_with($perm->name, 'scoreboard.'))
                                <span class="label label-primary">Scoreboard</span>&nbsp;
                                @endif
                                {{ $perm->display_name }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <p>
            {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\RolePermsController@edit', 'Edit Role', [$role->id], ['class' => 'btn btn-primary']) }}
            {{ link_to_route('site_role_del_confirm', 'Delete Role', [$role->id], ['class' => 'btn btn-danger']) }}
            {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\RolePermsController@index', 'Back To Rolelist', [], ['class' => 'btn bg-olive']) }}
        </p>
    </div>

    <div class="col-md-6">
        <p>Users assigned to this role:</p>

        <div class="box box-info">
            <div class="box-body table-responsive">
                <table class="table table-condensed">
                    <tbody>
                        @foreach($role->users() as $user)
                        <tr>
                            <td>{{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\UserController@edit', $user->username, [$user->id]) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@stop
