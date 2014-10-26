@section('content')
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="box box-primary">
                <div class="box-header">
                    <div class="box-title">{{ Lang::get('player.profile.section_titles.baisc') }}</div>
                </div>

                <div class="box-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label for="PID" class="col-sm-2 control-label">{{ Lang::get('player.profile.basic.id') }}</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">{{{ $player->PlayerID }}}</p>
                                <input type="hidden" id="PID" value="{{{ $player->PlayerID }}}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="gameIdent" class="col-sm-2 control-label">{{ Lang::get('player.profile.basic.game') }}</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">{{ $_gameIdent }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="EAGUID" class="col-sm-2 control-label">{{ Lang::get('player.profile.basic.eaguid') }}</label>
                            <div class="col-sm-10">
                                @if(Entrust::can('view_player_guids'))
                                <p class="form-control-static">
                                    <a href="/search?player={{{ $player->EAGUID }}}" tooltip-placement="top" tooltip="Click to find all players with this EAGUID">
                                        {{{ $player->EAGUID }}}
                                    </a>
                                @else
                                <p class="form-control-static">N/A</p>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="PBGUID" class="col-sm-2 control-label">{{ Lang::get('player.profile.basic.pbguid') }}</label>
                            <div class="col-sm-10">
                                @if(!empty($player->PBGUID) && Entrust::can('view_player_guids'))
                                <p class="form-control-static">{{{ $player->PBGUID }}}</p>
                                @else
                                <p class="form-control-static">N/A</p>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="IPADDR" class="col-sm-2 control-label">{{ Lang::get('player.profile.basic.ip') }}</label>
                            <div class="col-sm-10">
                                @if(!empty($player->IP_Address) && Entrust::can('view_player_ip'))
                                <p class="form-control-static">
                                    <a href="/search?player={{{ $player->IP_Address }}}" tooltip-placement="top" tooltip="Click to find all players with this IP">
                                        {{{ $player->IP_Address }}}
                                    </a>
                                </p>
                                @else
                                <p class="form-control-static">N/A</p>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="lastSeen" class="col-sm-2 control-label">{{ Lang::get('player.profile.basic.last_seen') }}</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">
                                    @if(isset($player->lastSeen()->LastSeenOnServer))
                                    <span data-livestamp="{{ Helper::UTCToLocal($player->lastSeen()->LastSeenOnServer)->toISO8601String() }}" tooltip-placement="top" tooltip="{{ Helper::UTCToLocal($player->lastSeen()->LastSeenOnServer)->format('M j, Y g:ia T') }}"></span>
                                    @else
                                    <span class="text-danger">N/A</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="country" class="col-sm-2 control-label">{{ Lang::get('player.profile.basic.country') }}</label>
                            <div class="col-sm-10">
                                @if(!empty($player->CountryCode) && $player->CountryName != FALSE)
                                <p class="form-control-static">
                                    <img src="{{ asset('img/flags/' . $player->CountryCode . '.png') }}" alt="{{ $player->CountryName }}" class="img-rounded" width="32"> {{ $player->CountryName }}
                                </p>
                                @else
                                <p class="form-control-static">
                                    <span class="text-danger">N/A</span>
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    @include('angular.public.battlefield.common.playercard-links')

                    <div ng-controller="PlayerInfoReputation">
                        <div id="graph-reputation" style="width: 300px; height: 200px" class="center-block" ng-show="loaded"></div>
                        <div class="alert alert-info" ng-hide="loaded">
                            <i class="icon ion-loading-c"></i>
                            Loading reputation... Please wait
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="nav-tabs-custom">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_infractions" data-toggle="tab">{{ Lang::get('player.profile.overview.infractions.title') }}</a></li>
                        <li><a href="#tab_banactive" data-toggle="tab">{{ Lang::get('player.profile.overview.bans.current.title') }}</a></li>
                        <li><a href="#tab_banprevious" data-toggle="tab">{{ Lang::get('player.profile.overview.bans.previous.title') }}</a></li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_infractions">
                            @if(count($player->pointsPerServer()) > 0)
                            <div class="box-body table-responsive" <?php if($_forgive) : ?> ng-controller="PlayerIssueForgive"<?php endif; ?>>
                                <table class="table table-condensed table-striped">
                                    <thead>
                                        <th width="250px">{{ Lang::get('player.profile.overview.infractions.table.col1') }}</th>
                                        <th>{{ Lang::get('player.profile.overview.infractions.table.col2') }}</th>
                                        <th>{{ Lang::get('player.profile.overview.infractions.table.col3') }}</th>
                                        <th>{{ Lang::get('player.profile.overview.infractions.table.col4') }}</th>
                                        @if($_forgive)
                                        <th></th>
                                        @endif
                                    </thead>
                                    <tbody>
                                        @foreach($player->pointsPerServer() as $infraction)
                                        <tr>
                                            <td>
                                                <span class="trim-server-name" tooltip-placement="top" tooltip="{{{ ADKGamers\Webadmin\Models\Battlefield\Server::find($infraction->server_id)->ServerName }}}">
                                                    {{{ ADKGamers\Webadmin\Models\Battlefield\Server::find($infraction->server_id)->ServerName }}}
                                                </span>
                                            </td>
                                            <td>{{{ $infraction->punish_points }}}</td>
                                            <td>{{{ $infraction->forgive_points }}}</td>
                                            <td>{{{ $infraction->total_points }}}</td>
                                            @if($_forgive)
                                            <td><button class="btn btn-xs bg-olive" ng-click="forgiveCheck({{$infraction->server_id}});">{{ Lang::get('player.profile.overview.infractions.forgive.text') }}</button></td>
                                            @endif
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                @if($_forgive)
                                <div ng-if="showActions" class="clearfix">
                                    <p class="help-text">{{ Lang::get('player.profile.overview.infractions.forgive.help') }}</p>
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <input type="text" ng-model="forgive.message" class="form-control" maxlength="100" value="ForgivePlayer">
                                        </div>
                                        <div class="col-xs-6">
                                            <input type="number" ng-model="forgive.xtimes" class="form-control" min="1" value="1">
                                        </div>
                                        <div class="col-xs-12">
                                            <br>
                                            <button class="btn btn-xs btn-success pull-right" ng-click="cancelForgive()" >{{ Lang::get('player.profile.overview.infractions.forgive.actions.cancel') }}</button>
                                            <button class="btn btn-xs btn-primary pull-right" data-loading-text="Please wait..." id="forgive_submit" ng-click="issueForgive()" >
                                                {{ Lang::get('player.profile.overview.infractions.forgive.actions.ok') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="box-footer clearfix text-center">
                                {{ Lang::get('player.profile.overview.infractions.total', [
                                    'playername' => $player->SoldierName,
                                    'total'      => $player->points()->total_points,
                                    'punish'     => $player->points()->punish_points,
                                    'forgive'    => $player->points()->forgive_points
                                ]) }}
                            </div>
                            @else
                            <div class="alert alert-info">{{ Lang::get('player.profile.overview.infractions.none', ['playername' => $player->SoldierName]) }}</div>
                            @endif
                        </div>

                        <div class="tab-pane" id="tab_banactive">
                            @if($player->recentBanExist())
                            <div class="table-responsive">
                                <table class="table table-condensed">
                                    <thead>
                                        <th width="180px">{{ Lang::get('player.profile.overview.bans.current.table.col1') }}</th>
                                        <th width="110px">{{ Lang::get('player.profile.overview.bans.current.table.col2') }}</th>
                                        <th width="230px">{{ Lang::get('player.profile.overview.bans.current.table.col3') }}</th>
                                        <th width="50px">{{ Lang::get('player.profile.overview.bans.current.table.col4') }}</th>
                                        <th>{{ Lang::get('player.profile.overview.bans.current.table.col5') }}</th>
                                        <th>{{ Lang::get('player.profile.overview.bans.current.table.col6') }}</th>
                                    </thead>

                                    <tbody>
                                        <tr>
                                            <td>{{ Helper::UTCToLocal($player->recentBan->ban_startTime)->format('M j, Y g:ia T') }}</td>
                                            <td><span data-livestamp="{{ strtotime($player->recentBan->ban_endTime) }}"></span></td>
                                            <td>
                                                <span class="trim-server-name" tooltip-placement="top" tooltip="{{$player->recentBan->info->server->ServerName}}">
                                                    {{$player->recentBan->info->server->ServerName}}
                                                </span>
                                            </td>
                                            <td>
                                                @if($player->recentBan->info->command_action == 8)
                                                <span class="label label-danger">Perm</span>
                                                @elseif($player->recentBan->info->command_action == 7)
                                                <span class="label label-warning">Temp</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($player->recentBan->ban_status == 'Active')
                                                <span class="label label-warning">{{$player->recentBan->ban_status}}</span>
                                                @elseif($player->recentBan->ban_status == 'Disabled')
                                                <span class="label label-default">{{$player->recentBan->ban_status}}</span>
                                                @elseif($player->recentBan->ban_status == 'Expired')
                                                <span class="label label-success">{{$player->recentBan->ban_status}}</span>
                                                @endif
                                            </td>
                                            <td>{{ $player->recentBan->info->record_message }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            @if(Entrust::can('manage_adkats_bans'))
                            {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\BanController@edit', Lang::get('player.profile.overview.bans.current.modify'), [$player->recentBan->ban_id], ['class' => 'btn btn-xs bg-olive']) }}
                            @endif
                            @else
                            <div class="alert alert-info" role="alert">
                                {{ Lang::get('player.profile.overview.bans.current.none', ['playername' => $player->SoldierName]) }}
                            </div>
                            @if(Entrust::can('manage_adkats_bans'))
                            <?php $create_ban_url = action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\BanController@create') . "?" . http_build_query(['id' => $player->PlayerID]); ?>
                            <a href="{{$create_ban_url}}" class="btn btn-xs bg-olive">{{ Lang::get('player.profile.overview.bans.current.issue') }}</a>
                            @endif
                            @endif
                        </div>

                        <div class="tab-pane" id="tab_banprevious">
                            @if($player->previousBans()->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-condensed">
                                    <thead>
                                        <th width="180px">{{ Lang::get('player.profile.overview.bans.current.table.col1') }}</th>
                                        <th width="110px">{{ Lang::get('player.profile.overview.bans.current.table.col2') }}</th>
                                        <th width="250px">{{ Lang::get('player.profile.overview.bans.current.table.col3') }}</th>
                                        <th width="50px">{{ Lang::get('player.profile.overview.bans.current.table.col4') }}</th>
                                        <th>{{ Lang::get('player.profile.overview.bans.current.table.col6') }}</th>
                                    </thead>

                                    <tbody>
                                        @foreach($player->previousBans() as $ban)
                                        <tr>
                                            <td>{{ Helper::UTCToLocal($ban->record_time)->format('M j, Y g:ia T') }}</td>
                                            <td><span data-livestamp="{{ strtotime($ban->record_time->addMinutes($ban->command_numeric)) }}"></span></td>
                                            <td>
                                                <span class="trim-server-name" tooltip-placement="top" tooltip="{{$ban->server->ServerName}}">
                                                    {{$ban->server->ServerName}}
                                                </span>
                                            </td>
                                            <td>
                                                @if($ban->command_action == 8 || $ban->command_action == 73)
                                                <span class="label label-danger">Perm</span>
                                                @elseif($ban->command_action == 7 || $ban->command_action == 72)
                                                <span class="label label-warning">Temp</span>
                                                @endif
                                            </td>
                                            <td>{{ $ban->record_message }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="alert alert-info" role="alert">
                                {{ Lang::get('player.profile.overview.bans.previous.none', ['playername' => $player->SoldierName]) }}
                            </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-body">
                    <div id="graph-pie"></div>
                </div>
            </div>
        </div>

    </div>

    <div class="row" ng-controller="PlayerInfoGraphs">
        <section ng-show="loaded">
            <div class="col-sm-12 col-md-12 col-lg-12" ng-hide="commandhistory.length == 0">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li><a href="#tab_1_table" data-toggle="tab">Table</a></li>
                        <li class="active"><a href="#tab_2_graph" data-toggle="tab">Graph</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane" id="tab_1_table">
                            @include('angular.public.battlefield.common.playercard-cmdhistory')
                        </div>
                        <div class="tab-pane active" id="tab_2_graph">
                            <div id="graph-spline" ng-hide="commandhistory.length == 0"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-md-12 col-lg-12" ng-hide="namehistory.length == 0 && iphistory.length == 0">
                <div class="box box-primary">
                    <div class="box-body">
                        @if( Entrust::can('view_player_ip') )
                        <div class="row">
                            <div class="col-sm-12 col-md-6 col-lg-6" ng-hide="namehistory.length == 0">
                                <div id="graph-pie-soldiers" ng-hide="namehistory.length == 0"></div>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6" ng-hide="iphistory.length == 0">
                                <div id="graph-pie-ips" ng-hide="iphistory.length == 0"></div>
                            </div>
                        </div>
                        @else
                        <div id="graph-pie-soldiers" ng-hide="namehistory.length == 0"></div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
        <section ng-hide="loaded">
            <div class="alert alert-info">
                <i class="icon ion-loading-c"></i>
                Loading graphs... Please wait
            </div>
        </section>
    </div>

    <div class="row">
        @include('angular.public.battlefield.common.playercard-stats')
    </div>

    <div class="row">
        @include('angular.public.battlefield.common.playercard-records')
    </div>

    <div class="row">
        @include('angular.public.battlefield.common.playercard-chatlog')
    </div>

    <div class="row">
        <div id="disqus_thread" class="col-sm-12"></div>
    </div>

@stop

@section('javascript')
@if(!empty($player->EAGUID))
<script type="text/javascript">
    /* * * DON'T EDIT BELOW THIS LINE * * */

    var disqus_shortname = 'bfadmincp';
    var disqus_identifier = '{{ sha1($player->EAGUID) }}';

    (function() {
        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
        dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
    })();
</script>
@endif
@stop

@section('jsinclude')
<script src="{{ asset('js/plugins/datatables/jquery.dataTables.js') }}"></script>
<script src="{{ asset('js/plugins/datatables/dataTables.bootstrap.js') }}"></script>
<script src="{{ asset('js/BFAdminCP/controllers/PlayerInfoCtrl.js') }}"></script>
@stop

@section('stylesinclude')
<link href="{{ asset('css/datatables/dataTables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('headermeta')
<meta name="revisit-after" content="3 days">
<meta name="keywords" content="bf3, bf4, bfadmincp, battlefield 3, battlefield 4, {{$player->SoldierName}}, player info, ban history">
@stop
