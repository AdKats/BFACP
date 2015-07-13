@extends('layout.main')

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">{{ $servers->count() }} Servers Found</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped table-condensed">
                        <thead>
                            <th width="50px">ID</th>
                            <th width="50px">Game</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Password Set</th>
                            <th>Address</th>
                            <th>RCON Port</th>
                        </thead>

                        <tbody>
                            @foreach($servers as $server)
                            <tr>
                                <td>{{ $server->ServerID }}</td>
                                <td>{{ Form::label(null, $server->game->Name, ['class' => $server->game->class_css]) }}</td>
                                <td>{{ link_to_route('admin.site.servers.edit', $server->ServerName, $server->ServerID, ['target' => '_self']) }}</td>
                                <td>
                                    @if($server->is_active)
                                    <label class="label bg-green">Enabled</label>
                                    @else
                                    <label class="label bg-red">Disabled</label>
                                    @endif
                                </td>
                                <td>
                                    @if(!is_null($server->setting) && !is_null($server->setting->rcon_password))
                                    <label class="label bg-green">Yes</label>
                                    @else
                                    <label class="label bg-red">No</label>
                                    @endif
                                </td>
                                <td>{{ $server->ip }}</td>
                                <td>{{ $server->port }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
