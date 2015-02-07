@extends('layout.main')

@section('content')

<div class="row" ng-controller="PlayerListController">

    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Player List
                    @if(Input::has('player'))
                    - <span popover="{{ Input::get('player') }}" popover-animation="true" popover-trigger="mouseenter">{{ str_limit(Input::get('player'), 30) }}</span>
                @endif</h3>
                <div class="box-tools pull-right form-inline">
                    <div class="form-group hidden-sm">
                        <label>Page
                            <input type="number" min="1" ng-model="main.page" class="form-control input-sm" ng-blur="getListing()" />
                        </label>
                    </div>

                    <div class="form-group hidden-xs">
                        <label>Show
                            <select ng-options="n for n in [] | range:10:100:10" ng-model="main.take" class="form-control" ng-change="getListing()"></select>
                        </label>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-primary btn-xs" ng-click="previousPage()" ng-disabled="main.page <= 1">
                            Previous
                        </button>
                        <button type="button" class="btn btn-primary btn-xs" ng-click="nextPage()" ng-disabled="main.page == main.last_page">
                            Next
                        </button>
                        {{ link_to_route('player.listing', 'Reset', [], ['class' => 'btn btn-success btn-xs', 'target' => '_self']) }}
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <th width="32px">ID</th>
                            <th width="32px">Game</th>
                            <th width="32px">Rank</th>
                            <th>Player</th>
                            <th>Ban</th>
                            <th class="hidden-sm">Reputation</th>
                            <th class="hidden-sm">Infractions</th>
                        </thead>
                        <tbody>
                            <tr ng-repeat="(key, player) in players">
                                <td ng-bind="player.PlayerID"></td>
                                <td><span ng-class="player.game.class_css" ng-bind="player.game.Name"></span></td>
                                <td ng-bind="player.GlobalRank"></td>
                                <td><a ng-href="@{{ player.profile_url }}" ng-bind="player.SoldierName"></a></td>
                                <td>
                                    <ng-switch on="player.ban !== null">
                                        <div ng-switch-when="true">
                                            <div ng-if="player.ban.is_active">
                                                <div ng-switch on="player.ban.is_perm">
                                                    <span ng-switch-when="true" ng-cloak class="label bg-red" tooltip="@{{ player.ban.record.record_message }}">
                                                        Permanently Banned
                                                    </span>
                                                    <span ng-switch-default>
                                                        Expires <span ng-cloak ng-bind="moment(player.ban.ban_expires).fromNow()" tooltip="@{{ player.ban.ban_expires | date: 'medium' }}" class="label bg-purple"></span>
                                                    </span>
                                                </div>
                                            </div>

                                            <div ng-if="player.ban.ban_status == 'Disabled'">
                                                <span class="label label-default">
                                                    Ban Disabled
                                                </span>
                                            </div>

                                            <span ng-if="player.ban.is_expired" ng-cloak class="label bg-blue" tooltip="@{{ player.ban.ban_expires | date: 'medium' }}">
                                                Ban expired @{{ moment(player.ban.ban_expires).fromNow() }}
                                            </span>
                                        </div>

                                        <div ng-switch-default>
                                            <span class="label bg-green">No ban on file</span>
                                        </div>
                                    </ng-switch>
                                </td>
                                <td class="hidden-sm">
                                    <span class="label" ng-class="reputation(player.reputation.total_rep_co)" ng-bind="player.reputation.total_rep_co.toFixed(2)"></span>
                                </td>
                                <td class="hidden-sm">
                                    <ng-switch on="player.infractions_global !== null">
                                        <span ng-switch-when="true" ng-bind="player.infractions_global.total_points"></span>
                                        <span ng-switch-default>No Infractions</span>
                                    </ng-switch>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box-footer clearfix" ng-if="main.total !== null">
                <div class="pull-left">Page <span ng-bind="main.page"></span> of <span ng-bind="main.last_page"></span></div>
                <div class="pull-right">Total: <span ng-bind="main.total"></span></div>
            </div>

            <div class="overlay" ng-if="!loaded">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </div>
    </div>

</div>

@stop
