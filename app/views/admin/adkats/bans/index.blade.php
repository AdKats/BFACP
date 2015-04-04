@extends('layout.main')

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped table-condensed">
                        <thead>
                            <th>{{ Lang::get('adkats.bans.listing.table.col1') }}</th>
                            <th>{{ Lang::get('adkats.bans.listing.table.col2') }}</th>
                            <th>{{ Lang::get('adkats.bans.listing.table.col3') }}</th>
                            <th class="hidden-sm">{{ Lang::get('adkats.bans.listing.table.col4') }}</th>
                            <th>{{ Lang::get('adkats.bans.listing.table.col5') }}</th>
                            <th>{{ Lang::get('adkats.bans.listing.table.col6') }}</th>
                            <th>{{ Lang::get('adkats.bans.listing.table.col7') }}</th>
                            <th class="hidden-sm">{{ Lang::get('adkats.bans.listing.table.col8') }}</th>
                            <th>{{ Lang::get('adkats.bans.listing.table.col9') }}</th>
                        </thead>

                        <tbody>
                            @foreach($bans as $ban)
                            <tr>
                                <td>{{ link_to_route('admin.adkats.bans.edit', $ban->ban_id, [$ban->ban_id]) }}</td>
                                <td><span class="{{ $ban->player->game->class_css }}">{{ $ban->player->game->Name }}</span>
                                <td>{{ link_to_route('player.show', $ban->player->SoldierName, [$ban->player->PlayerID, $ban->player->SoldierName]) }}</td>
                                <td class="hidden-sm">
                                    @if(!is_null($ban->record->source_id))
                                    {{ link_to_route('player.show', $ban->record->source_name, [$ban->record->source_id, $ban->record->source_name]) }}
                                    @else
                                    {{ $ban->record->source_name }}
                                    @endif
                                </td>
                                <td>
                                    @if($ban->is_active)
                                    <label class="label label-danger">{{ Lang::get('player.profile.bans.status.enabled') }}</label>
                                    @elseif($ban->is_expired)
                                    <label class="label label-success">{{ Lang::get('player.profile.bans.status.expired') }}</label>
                                    @elseif( ! $ban->is_active && ! $ban->is_expired)
                                    <label class="label label-primary">{{ Lang::get('player.profile.bans.status.disabled') }}</label>
                                    @endif
                                </td>
                                <td>
                                    <span ng-bind="moment('{{ $ban->ban_issued }}').fromNow()" tooltip="<?php echo '{{'; ?> moment('<?php echo $ban->ban_issued; ?>').format('lll') <?php echo '}}'; ?>"></span>
                                </td>
                                <td>
                                    @if($ban->is_perm)
                                    <label class="label label-danger">{{ Lang::get('player.profile.bans.type.permanent.long') }}</label>
                                    @else
                                    <span ng-bind="moment('{{ $ban->ban_expires }}').fromNow()" tooltip="<?php echo '{{'; ?> moment('<?php echo $ban->ban_expires; ?>').format('lll') <?php echo '}}'; ?>"></span>
                                    @endif
                                </td>
                                <td class="hidden-sm">
                                    @if($ban->ban_enforceName)
                                    <span class="badge bg-green">Name</span>
                                    @endif
                                    @if($ban->ban_enforceGUID)
                                    <span class="badge bg-green">GUID</span>
                                    @endif
                                    @if($ban->ban_enforceIP)
                                    <span class="badge bg-green">IP</span>
                                    @endif
                                </td>
                                <td>{{ $ban->record->record_message }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box-footer clearfix">
                <div class="pull-left">Total: <span ng-bind="{{ $bans->getTotal() }} | number"></span></div>
                <div class="pull-right">{{ $bans->links() }}</div>
            </div>
        </div>
    </div>
</div>
@stop
