@extends('layout.main')

@section('content')
    {!! Former::open()->route('admin.site.servers.update', [$server->ServerID]) !!}
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-body">
                    {!! Former::text('filter')
                        ->label('Filter Server Name')
                        ->help('Fill out this field to strip out unwanted characters in server name. This only affects how the server name is displayed on the site. Separate each entry by a comma.') !!}

                    {!! Former::text('rcon_password')
                        ->label('RCON Password')
                        ->help('Enter RCON password for server. This is required for the Live Scoreboard to function. Leave field blank to not change it.')
                        ->forceValue('') !!}

                    {!! Former::text('battlelog_guid')
                        ->label('Battlelog GUID')
                        ->help('Battlelog GUID of your server. Required for certain information to be displayed.') !!}

                    @if(Config::get('uptimerobot.enabled'))
                        <div class="form-group">
                            <label for="use_uptimerobot" class="col-sm-2 control-label">Enable Monitor</label>

                            <div class="col-sm-10">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="use_uptimerobot" value="1" @if(!empty($server->setting->monitor_key)) checked @endif>
                                        Enabled
                                    </label>
                                </div>

                                <div class="help-block">
                                    If checked this will setup this server on UptimeRobot.
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="form-inline">
                        <label for="server_status" class="col-sm-2 control-label">Status</label>

                        <div class="col-sm-10">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="server_status" value="on" @if($server->is_active) checked @endif>
                                    Active
                                </label>
                                &nbsp;
                                <label>
                                    <input type="radio" name="server_status" value="off" @if(! $server->is_active) checked @endif>
                                    Inactive
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-offset-2 col-sm-offset-4 col-lg-10 col-sm-8">
                            <button type="submit" class="btn bg-green">
                                <i class="fa fa-floppy-o"></i>&nbsp;<span>{{ trans('site.admin.servers.edit.buttons.save') }}</span>
                            </button>
                            {!! link_to_route('admin.site.servers.index', trans('site.admin.servers.edit.buttons.cancel'), [], ['class' => 'btn bg-blue', 'target' => '_self']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Former::close() !!}
@stop
