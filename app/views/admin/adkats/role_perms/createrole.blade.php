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
        @foreach($errors->all() as $error)
            <div class="alert alert-danger">
                <i class="fa fa-ban"></i>
                <b>Error!</b> {{ $error }}
            </div>
        @endforeach

        <div class="box box-primary">
            {{ Form::open(array('action' => 'ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\RolePermsController@store')) }}
            <div class="box-body">

                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-block">
                            {{ Form::label('role_name', 'Role Name') }} <span class="label label-danger pull-right">Required</span>
                            {{ Form::text('role_name', Session::get('role_name', NULL), ['class' => 'form-control', 'required' => 'required', 'ng-model' => 'role.name']) }}
                        </div>

                        <div class="form-block">
                            <dl class="dl-horizontal">
                                @foreach($permissions as $permission)
                                <dt>
                                    <small data-toggle="tooltip" data-placement="top" title="{{ $permission['info']->command_name }}">
                                        {{ $permission['info']->command_name }}
                                    </small>
                                </dt>
                                <dd>
                                    <label>
                                        {{ Form::checkbox('perms[]', $permission['info']->command_id, $permission['hasAccess']) }}
                                        @if($permission['info']->command_playerInteraction)
                                        <span class="label label-danger">Admin</span>
                                        @endif
                                    </label>
                                </dd>
                                @endforeach
                            </dl>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="alert alert-info">
                            Selecting an admin command to be used for the <span class="label label-success">@{{ role.name }}</span> role will make that role an administrator group.
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer clearfix">
                {{ Form::submit('Create Role', array('class' => 'btn btn-primary')) }}
                {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\RolePermsController@index', 'Back To Rolelist', [], ['class' => 'btn bg-olive']) }}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

@stop
