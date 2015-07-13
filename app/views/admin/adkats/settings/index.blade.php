@extends('layout.main')

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped table-condensed">
                        <thead>
                            <th>ID</th>
                            <th>Server</th>
                        </thead>

                        <tbody>
                            @foreach($servers as $server)
                            <tr>
                                <td>{{ $server->ServerID }}</td>
                                <td>{{ link_to_route('admin.adkats.settings.edit', $server->ServerName, $server->ServerID, ['target' => '_self']) }}</td>
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
