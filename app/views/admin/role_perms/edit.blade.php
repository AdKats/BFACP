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
            {{ Form::open(array('action' => array('ADKGamers\\Webadmin\\Controllers\\Admin\\RolePermsController@update', $role->id), 'method' => 'put')) }}
            <div class="box-body">

                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-block">
                            {{ Form::label('role_name', 'Role Name') }} <span class="label label-danger pull-right">Required</span>
                            {{ Form::text('role_name', $role->name, ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <br>
                        <div class="form-block">
                            {{ Form::label('command_checks', 'Allowed Commands') }}
                            <dl class="dl-horizontal checkbox">
                                @foreach($permissions as $permid => $permission)
                                <dt>
                                    <small data-toggle="tooltip" data-placement="left" title="{{ $permission['info']->display_name }}">
                                        {{ $permission['info']->display_name }}
                                    </small>
                                </dt>
                                <dd>
                                    <label>
                                        {{ Form::checkbox('perms[]', $permid, $permission['hasAccess']) }}
                                        @if(starts_with($permission['info']->name, 'scoreboard.'))
                                        <span class="label label-primary">Scoreboard</span>
                                        @endif
                                    </label>
                                </dd>
                                @endforeach
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer clearfix">
                {{ Form::submit('Update Role', array('class' => 'btn btn-primary')) }}
                {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\RolePermsController@show', 'Cancel Update', [$role->id], ['class' => 'btn btn-primary']) }}
                {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\RolePermsController@index', 'Back To Rolelist', [], ['class' => 'btn bg-olive']) }}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

@stop

