@extends('layout.main')

@section('content')

<div class="row">

    <!-- Player Basic -->
    <div class="col-xs-12 col-md-6">
        <div class="box box-primary">
            <div class="box-header">
                <div class="box-title">Details</div>
            </div>

            <div class="box-body">
                <div class="form-horizontal">

                    <div class="form-group">
                        <label class="col-sm-3 control-label">ID</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">{{{ $player->PlayerID }}}</p>
                            {{ Form::hidden('player_id', $player->PlayerID) }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Game</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">{{{ $player->game->Name }}}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">EA GUID</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                @if( ! empty($player->EAGUID) )
                                {{ link_to_route('player.listing', $player->EAGUID, [
                                    'player' => $player->EAGUID
                                ]) }}
                                @else
                                <span class="text-red">N/A</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">PB GUID</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                @if( ! empty($player->PBGUID) )
                                {{ link_to_route('player.listing', $player->PBGUID, [
                                    'player' => $player->PBGUID
                                ]) }}
                                @else
                                <span class="text-red">N/A</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">IP</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                @if( ! empty($player->IP_Address) )
                                {{ link_to_route('player.listing', $player->IP_Address, [
                                    'player' => $player->IP_Address
                                ]) }}
                                @else
                                <span class="text-red">N/A</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Country</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                {{ HTML::image($player->country_flag, $player->country_name) }}
                                {{{ $player->country_name }}}
                            </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Reputation</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <span class="{{ $player->reputation->color }}">
                                    @if($player->reputation->total_rep_co > 0)
                                    <i class="fa fa-caret-up"></i>
                                    @elseif($player->reputation->total_rep_co < 0)
                                    <i class="fa fa-caret-down"></i>
                                    @elseif($player->reputation->total_rep_co == 0)
                                    <i class="fa fa-caret-left"></i>
                                    @endif

                                    {{ round($player->reputation->total_rep_co, 2) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Rank</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                {{ HTML::image($player->rank_image, sprintf('Rank %u', $player->GlobalRank), ['width' => '128px']) }}
                            </p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
    <!-- END Player Basic -->

    <div class="col-xs-12 col-md-6">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="javascript://" data-target="#infractions" data-toggle="tab">Infractions</a></li>
                <li><a href="javascript://" data-target="#ban-current" data-toggle="tab">Current Ban</a></li>
                <li><a href="javascript://" data-target="#ban-previous" data-toggle="tab">Previous Bans</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="infractions">
                    @if( ! is_null($player->infractions_global) && ! is_null($player->infractions_server))
                    <table class="table table-striped table-condensed">
                        <thead>
                            <th>Server</th>
                            <th>Punishes</th>
                            <th>Forgives</th>
                            <th>Total</th>
                        </thead>

                        <tbody>
                            @foreach($player->infractions_server as $infraction)
                            <tr>
                                <td>{{ $infraction->server->server_name_short or str_limit($infraction->server->ServerName, 30) }}</td>
                                <td>{{ $infraction->punish_points }}</td>
                                <td>{{ $infraction->forgive_points }}</td>
                                <td>{{ $infraction->total_points }}</td>
                            </tr>
                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr>
                                <td><span class="pull-right">Total</span></td>
                                <td>{{ $player->infractions_global->punish_points }}</td>
                                <td>{{ $player->infractions_global->forgive_points }}</td>
                                <td>{{ $player->infractions_global->total_points }}</td>
                            </tr>
                        </tfoot>
                    </table>
                    @else
                    <div class="alert alert-success">
                        <i class="fa fa-check"></i> No infractions on file
                    </div>
                    @endif
                </div>

                <div class="tab-pane" id="ban-current">
                    @if( ! is_null($player->ban) )
                    <table class="table table-striped table-condensed">
                        <thead>
                            <th>Issued</th>
                            <th>Expires</th>
                            <th>Server</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th width="25%">Reason</th>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    <span ng-bind="moment('{{ $player->ban->ban_issued }}').fromNow()" tooltip="<?php echo '{{'; ?> moment('<?php echo $player->ban->ban_issued; ?>').format('lll') <?php echo '}}'; ?>"></span>
                                </td>
                                <td>
                                    <span ng-bind="moment('{{ $player->ban->ban_expires }}').fromNow()" tooltip="<?php echo '{{'; ?> moment('<?php echo $player->ban->ban_expires; ?>').format('lll') <?php echo '}}'; ?>"></span>
                                </td>
                                <td>{{ $player->ban->record->server->server_name_short or str_limit($player->ban->record->server->ServerName, 30) }}</td>
                                <td>
                                    @if($player->ban->is_perm)
                                    <label class="label label-danger">Perm</label>
                                    @else
                                    <label class="label label-warning">Temp</label>
                                    @endif
                                </td>
                                <td>
                                    @if($player->ban->is_active)
                                    <label class="label label-danger">Enabled</label>
                                    @elseif($player->ban->is_expired)
                                    <label class="label label-success">Expired</label>
                                    @elseif( ! $player->ban->is_active && ! $player->ban->is_expired)
                                    <label class="label label-primary">Disabled</label>
                                    @endif
                                </td>
                                <td>{{ $player->ban->record->record_message }}</td>
                            </tr>
                        </tbody>
                    </table>
                    @else
                    <div class="alert alert-success">
                        <i class="fa fa-check"></i> No ban on file
                    </div>
                    @endif
                </div>

                <div class="tab-pane" id="ban-previous">
                    @if( ! is_null($player->ban) && ! is_null($player->ban->previous) && $player->ban->latest_record_id != $player->ban->previous[0]->record_id )
                    <table class="table table-striped table-condensed">
                        <thead>
                            <th>Issued</th>
                            <th>Expired</th>
                            <th>Server</th>
                            <th>Type</th>
                            <th width="25%">Reason</th>
                        </thead>

                        <tbody>
                            @foreach($player->ban->previous as $ban)
                            <?php if($player->ban->latest_record_id == $ban->record_id) continue; ?>
                            <tr>
                                <td>
                                    <span ng-bind="moment('{{ $ban->stamp }}').fromNow()" tooltip="<?php echo '{{'; ?> moment('<?php echo $ban->stamp; ?>').format('lll') <?php echo '}}'; ?>"></span>
                                </td>
                                <td>
                                    <span ng-bind="moment('{{ $ban->record_time->addMinutes($ban->command_numeric)->toIso8601String() }}').fromNow()" tooltip="<?php echo '{{'; ?> moment('<?php echo $ban->record_time->addMinutes($ban->command_numeric)->toIso8601String(); ?>').format('lll') <?php echo '}}'; ?>"></span>
                                </td>
                                <td>{{ $ban->server->server_name_short or str_limit($ban->server->ServerName, 30) }}</td>
                                <td>
                                    @if($ban->command_action == 73)
                                    <label class="label label-danger">Perm</label>
                                    @else
                                    <label class="label label-warning">Temp</label>
                                    @endif
                                </td>
                                <td>{{ $ban->record_message }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="alert alert-success">
                        <i class="fa fa-check"></i> No previous bans on file
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>


@stop
