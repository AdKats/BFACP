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

    @foreach($errors->all() as $error)
        <div class="col-md-12">
            <div class="alert alert-danger">
                <i class="fa fa-ban"></i>
                <b>Error!</b> {{ $error }}
            </div>
        </div>
    @endforeach

    @if(empty($server->setting->rcon_pass_hash))
    <div class="col-md-12">
        <div class="callout callout-warning">
            <h4>Notice!</h4>
            <p>No RCON password has been set. This happens when you just added the server or when the field is blank in the database.</p>
        </div>
    </div>
    @endif

    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <div class="box-title">
                    {{ $server->ServerName }}
                </div>
            </div>
            {{ Form::open(array('action' => array('ADKGamers\\Webadmin\\Controllers\\Admin\\SiteGameServerController@update', $server->ServerID), 'method' => 'put', 'class' => 'form-horizontal')) }}
                <div class="box-body">
                    <div class="form-group">
                        {{ Form::label('rcon_password', 'RCON Password', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::text('rcon_password', NULL, ['class' => 'form-control']) }}
                            <p class="help-block">Leave blank to keep current password. Password will be hashed before being stored in the database.</p>
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('namestrip', 'Filter Server Name', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::text('namestrip', $server->setting->name_strip, ['class' => 'form-control']) }}
                            <p class="help-block">Add any word or character you want to remove from the server name and seprate each one by a comma (,). If you do not want to filter them just leave this blank.</p>
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('enable_server', 'Status', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            <div class="radio">
                                <label>
                                    {{ Form::radio('enable_server', 'on', ( $server->ConnectionState == 'on' ? TRUE : FALSE ), ['class' => 'form-control']) }}
                                    Enabled
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    {{ Form::radio('enable_server', 'off', ( $server->ConnectionState == 'off' ? TRUE : FALSE ), ['class' => 'form-control']) }}
                                    Disabled
                                </label>
                            </div>
                            <p class="help-block">Enable or Disable this server from showing on the BFAdminCP. Records/Chatlogs will be unaffected when viewing them.</p>
                        </div>
                    </div>

                    @if(empty($server->setting->uptime_robot_id))
                    <div class="form-group">
                        <div class="col-sm-11 col-sm-offset-1">
                            <div class="radio">
                                <label>
                                    {{ Form::checkbox('useUptimeRobot', 1, FALSE, ['class' => 'form-control']) }}
                                    Add this server to Uptime Robot
                                </label>
                            </div>
                            <p class="help-block">You must have setup your API keys for Uptime Robot.</p>
                        </div>
                    </div>
                    @endif

                </div>

                <div class="box-footer clearfix">
                    {{ Form::submit('Save Changes', array('class' => 'btn btn-primary')) }}
                    {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\SiteGameServerController@index', 'Cancel', [], ['class' => 'btn bg-olive']) }}
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop
