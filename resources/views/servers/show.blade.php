@extends('layout.main')

@section('content')
    <input type="hidden" id="server_id" value="{{ $server->ServerID }}">

    <section ng-controller="ServerController">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-header">
                        <h3 class="box-title">
                            <span class="{{ $server->game->class_css }}">{{ $server->game->Name }}</span>
                            {{ $server->server_name_short or $server->ServerName }}
                            - Round (<span ng-bind="{{ $server->rounds()->current() }} | number"></span>)
                        </h3>

                        <div ng-if="loading" class="box-tools pull-right animate-if" ng-cloak>
                            <i class="fa fa-cog fa-lg fa-spin"></i><strong> Loading...</strong>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="table-responsive">
                                    <table class="table table-striped table-condensed" ng-table="maps.table" show-filter="true">
                                        <tbody>
                                            <tr ng-repeat="(key, map) in $data">
                                                <td ng-bind="moment(map.map_load).format('lll')" sortable="'map_load'" data-title="'Map Loaded'"></td>
                                                <td ng-bind="moment(map.round_start).format('lll')" sortable="'round_start'" data-title="'Round Started'"></td>
                                                <td ng-bind="moment(map.round_end).format('lll')" sortable="'round_end'" data-title="'Round Ended'"></td>
                                                <td ng-bind="map.map_name" data-title="'Map'"></td>
                                                <td ng-bind="map.gamemode" data-title="'Mode'"></td>
                                                <td ng-bind="map.rounds" data-title="'Rounds'"></td>
                                                <td ng-bind="map.players.min" data-title="'Min Players'"></td>
                                                <td ng-bind="map.players.avg" data-title="'Avg Players'"></td>
                                                <td ng-bind="map.players.max" data-title="'Max Players'"></td>
                                                <td ng-bind="map.players.join" data-title="'Players Joined'"></td>
                                                <td ng-bind="map.players.left" data-title="'Players Left'"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12" id="population-history"></div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12" id="popular-maps"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
