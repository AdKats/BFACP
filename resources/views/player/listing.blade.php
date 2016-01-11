@extends('layout.main')

@section('content')

<div class="row" ng-controller="PlayerListController">

    <div class="col-xs-12">
        <alert ng-repeat="alert in alerts" type="@{{ alert.type }}" close="closeAlert($index)" dismiss-on-timeout="@{{ alert.timeout }}" class="animated fadeInDown">@{{ alert.msg }}</alert>
    </div>

    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Results</h3>
                <div class="box-tools pull-right form-inline">
                    <div class="form-group hidden-xs hidden-sm">
                        <label>Page
                            <input type="number" min="1" ng-model="main.page" class="form-control input-sm" ng-disabled="main.last_page == 1" ng-change="getListing()" />
                        </label>
                    </div>

                    <div class="form-group hidden-xs hidden-xs">
                        <label>Show
                            <select ng-options="n for n in [] | range:10:100:10" ng-model="main.take" class="form-control" ng-change="main.page = 1; getListing()"></select>
                        </label>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-primary btn-xs" ng-click="previousPage()" ng-disabled="main.page <= 1">
                            {{ Lang::get('tables.playerlist.button_previous') }}
                        </button>
                        <button type="button" class="btn btn-primary btn-xs" ng-click="nextPage()" ng-disabled="main.page == main.last_page">
                            {{ Lang::get('tables.playerlist.button_next') }}
                        </button>
                        {{ link_to_route('player.listing', Lang::get('tables.playerlist.button_reset'), [], ['class' => 'btn btn-success btn-xs', 'target' => '_self']) }}
                    </div>
                </div>
            </div>

            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                        <th width="32px">{{ Lang::get('tables.playerlist.columns.col1') }}</th>
                        <th width="32px">{{ Lang::get('tables.playerlist.columns.col2') }}</th>
                        <th width="32px">{{ Lang::get('tables.playerlist.columns.col3') }}</th>
                        <th width="300px">{{ Lang::get('tables.playerlist.columns.col4') }}</th>
                        <th>{{ Lang::get('tables.playerlist.columns.col5') }}</th>
                        <th class="hidden-xs hidden-sm">{{ Lang::get('tables.playerlist.columns.col6') }}</th>
                        <th class="hidden-xs hidden-sm">{{ Lang::get('tables.playerlist.columns.col7') }}</th>
                        <th class="hidden-xs">{{ Lang::Get('tables.playerlist.columns.col8') }}</th>
                    </thead>
                    <tbody>
                        <tr ng-repeat="(key, player) in players">
                            <td ng-bind="player.PlayerID"></td>
                            <td><span ng-class="player.game.class_css" ng-bind="player.game.Name"></span></td>
                            <td>
                                <img ng-src="@{{ player.rank_image }}" tooltip="Rank @{{ player.GlobalRank }}" width="32px">
                            </td>
                            <td>
                                <span ng-if="player.ClanTag !== null">
                                    [<span ng-bind="player.ClanTag"></span>]
                                </span>
                                <a ng-href="@{{ player.profile_url }}" ng-bind="player.SoldierName" target="_self"></a>
                            </td>
                            <td>
                                <ng-switch on="player.ban !== null">
                                    <div ng-switch-when="true">
                                        <div ng-if="player.ban.is_active">
                                            <div ng-switch on="player.ban.is_perm">
                                                <span ng-switch-when="true" ng-cloak class="label bg-red" tooltip="@{{ player.ban.record.record_message }}">
                                                    {{ Lang::get('player.profile.bans.type.permanent.long') }}
                                                </span>
                                                <span ng-switch-default class="label bg-purple">
                                                    {{ Lang::get('player.profile.bans.status.expire') }} <span ng-cloak ng-bind="moment(player.ban.ban_expires).fromNow()" tooltip="@{{ player.ban.ban_expires | date: 'medium' }}"></span>
                                                </span>
                                            </div>
                                        </div>

                                        <div ng-if="player.ban.ban_status == 'Disabled'">
                                            <span class="label label-default">
                                                {{ Lang::get('player.profile.bans.status.disabled') }}
                                            </span>
                                        </div>

                                        <span ng-if="player.ban.is_expired" ng-cloak class="label bg-blue" tooltip="@{{ player.ban.ban_expires | date: 'medium' }}">
                                            {{ Lang::get('player.profile.bans.status.expired') }} <span ng-bind="moment(player.ban.ban_expires).fromNow()"></span>
                                        </span>
                                    </div>

                                    <div ng-switch-default>
                                        <span class="label bg-green">{{ Lang::get('player.profile.bans.current.none') }}</span>
                                    </div>
                                </ng-switch>
                            </td>
                            <td class="hidden-xs hidden-sm">
                                <ng-switch on="player.reputation !== null">
                                    <span class="label" ng-switch-when="true" ng-class="player.reputation.color">
                                        <i class="fa" ng-class="{'fa-caret-up': player.reputation.total_rep_co > 0, 'fa-caret-down': player.reputation.total_rep_co < 0, 'fa-caret-left': player.reputation.total_rep_co === 0}"></i>

                                        <span ng-bind="player.reputation.total_rep_co.toFixed(2)"></span>
                                    </span>
                                    <span ng-switch-default class="label label-default">???</span>
                                </ng-switch>
                            </td>
                            <td class="hidden-xs hidden-sm">
                                <ng-switch on="player.infractions_global !== null">
                                    <span ng-switch-when="true" ng-bind="player.infractions_global.total_points" class="label bg-navy"></span>
                                    <span ng-switch-default class="label bg-green">{{ Lang::get('player.profile.infractions.none') }}</span>
                                </ng-switch>
                            </td>
                            <td class="hidden-xs"><img ng-src="@{{ player.country_flag }}" tooltip="@{{ player.country_name }}"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="box-footer clearfix" ng-if="main.total !== null">
                <div class="pull-left">Page <span ng-bind="main.page | number"></span> of <span ng-bind="main.last_page | number"></span></div>
                <div class="pull-right">Total: <span ng-bind="main.total | number"></span></div>
            </div>

            <div class="overlay" ng-if="!loaded">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </div>
    </div>

</div>

@stop
