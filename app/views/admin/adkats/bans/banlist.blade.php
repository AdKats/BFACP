@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-body table-responsive">
                <table class="table table-condensed">
                    <thead>
                        <th>Player</th>
                        <th class="hidden-sm">Admin</th>
                        <th>Status</th>
                        <th>Issued</th>
                        <th>Expires</th>
                        <th class="hidden-sm">Enforce Name</th>
                        <th class="hidden-sm">Enforce GUID</th>
                        <th class="hidden-sm">Enforce IP</th>
                        <th>Reason</th>
                        <th width="80px">Actions</th>
                    </thead>

                    <tbody>
                        @foreach($bans as $ban)
                        <tr>
                            <td>
                                [{{ $ban->Name }}]
                                @if(!is_null($ban->target_id))
                                {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\PlayerController@showInfo', $ban->target_name, [$ban->target_id, $ban->target_name]) }}
                                @else
                                {{ $ban->target_name }}
                                @endif
                            </td>
                            <td class="hidden-xs hidden-sm">
                                @if(!is_null($ban->source_id))
                                {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\PlayerController@showInfo', $ban->source_name, [$ban->source_id, $ban->source_name]) }}
                                @else
                                {{ $ban->source_name }}
                                @endif
                            </td>
                            <td>
                                @if($ban->ban_status == 'Active')
                                <span class="label label-warning">Active</span>
                                @elseif($ban->ban_status == 'Disabled')
                                <span class="label label-default">Disabled</span>
                                @elseif($ban->ban_status == 'Expired')
                                <span class="label label-success">Expired</span>
                                @endif
                            </td>
                            <td>{{ Helper::UTCToLocal($ban->ban_startTime)->format('M j, Y g:ia T') }}</td>
                            <td>{{ Helper::UTCToLocal($ban->ban_endTime)->format('M j, Y g:ia T') }}</td>
                            <td class="hidden-sm">
                                @if($ban->ban_enforceName == 'Y')
                                <span class="label label-success">Yes</span>
                                @else
                                <span class="label label-danger">No</span>
                                @endif
                            </td>
                            <td class="hidden-sm">
                                @if($ban->ban_enforceGUID == 'Y')
                                <span class="label label-success">Yes</span>
                                @else
                                <span class="label label-danger">No</span>
                                @endif
                            </td>
                            <td class="hidden-sm">
                                @if($ban->ban_enforceIP == 'Y')
                                <span class="label label-success">Yes</span>
                                @else
                                <span class="label label-danger">No</span>
                                @endif
                            </td>
                            <td>{{ $ban->record_message }}</td>

                            <td>{{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\BanController@edit', 'Edit', [$ban->ban_id], ['class' => 'btn btn-xs bg-olive']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="box-footer clearfix">
                <div class="pull-left">
                    Showing {{ number_format($bans->getFrom()) }} to {{ number_format($bans->getTo()) }} of {{ number_format($bans->getTotal()) }}
                </div>
                <div class="pull-right">
                    {{ $bans->links('overrides.slider-3-small') }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop
