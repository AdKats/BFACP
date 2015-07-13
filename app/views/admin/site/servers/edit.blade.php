@extends('layout.main')

@section('content')
{{ Former::open()->route('admin.site.servers.update', [$server->ServerID]) }}
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-body">
                {{ Former::text('filter')
                    ->label('Filter Server Name')
                    ->help('Fill out this field to strip out unwanted characters in server name. This only affects how the server name is displayed on the site. Seperate each entry by a comma.') }}

                {{ Former::text('rcon_password')
                    ->label('RCON Password')
                    ->help('Enter RCON password for server. This is required for the Live Scoreboard to function. Leave field blank to not change it.')
                    ->forceValue(null) }}

                {{ Former::text('battlelog_guid')
                    ->label('Battlelog GUID')
                    ->help('Battlelog GUID of your server. Required for certain information to be displayed.') }}

                {{ Former::inline_radios('server_status')->label('Status')->radios([
                    'Inactive' => ['name' => 'status', 'value' => 'off'],
                    'Active'   => ['name' => 'status', 'value' => 'on']
                ])->check([
                    'off' => !$server->is_active,
                    'on' => $server->is_active
                ]) }}

                <div class="form-group">
                    <div class="col-lg-offset-2 col-sm-offset-4 col-lg-10 col-sm-8">
                        <button type="submit" class="btn bg-green">
                            <i class="fa fa-floppy-o"></i>&nbsp;<span>{{ Lang::get('site.admin.servers.edit.buttons.save') }}</span>
                        </button>
                        {{ link_to_route('admin.site.servers.index', Lang::get('site.admin.servers.edit.buttons.cancel'), [], ['class' => 'btn bg-blue', 'target' => '_self']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{ Former::close() }}
@stop
