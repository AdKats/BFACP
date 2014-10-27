@section('content')

<div class="row">

    <div class="col-md-12">
        <div class="callout callout-info">
            <h4>Notice</h4>
            <p>This section does not actually modify any game server settings. This section is made to control which servers you want enabled and configured on the BFAdminCP.</p>
        </div>
    </div>

    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <div class="box-title">{{ $servers->count() }} Servers Found</div>
            </div>
            <div class="box-body">
                <table class="table table-stripe table-condensed">
                    <thead>
                        <th>ID</th>
                        <th width="30px">Game</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>IP/Hostname</th>
                        <th>RCON Port</th>
                    </thead>

                    <tbody>
                        @foreach($servers as $server)
                        <tr>
                            <td>{{ $server->ServerID }}</td>
                            <td><span class="label <?php echo $server->Name == 'BF3' ? 'bg-blue' : 'bg-maroon'; ?>">{{ $server->Name }}</span></td>
                            <td>{{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\SiteGameServerController@edit', $server->ServerName, [$server->ServerID]) }}</td>
                            <td>
                                @if($server->ConnectionState == 'on')
                                <span class="label label-success">Enabled</span>
                                @else
                                <span class="label label-danger">Disabled</span>
                                @endif
                            </td>
                            <td>{{ Helper::getIpAddr($server->IP_Address) }}</td>
                            <td>{{ Helper::getPort($server->IP_Address) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@stop
