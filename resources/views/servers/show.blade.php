@extends('layout.main')

@section('content')
    <input type="hidden" id="server_id" value="{{ $server->ServerID }}">

    <section ng-controller="ServerController">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-header">
                        <h3 class="box-title">&nbsp;</h3>

                        <div ng-if="loading" class="box-tools pull-right animate-if" ng-cloak>
                            <i class="fa fa-cog fa-lg fa-spin"></i><strong>&nbsp;{{ trans('common.loading') }}</strong>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="table-responsive">
                                    <table class="table table-striped table-condensed" ng-table="maps.table" show-filter="true">
                                        <tbody>
                                            <tr ng-repeat="(key, map) in $data">
                                                <td ng-bind="moment(map.map_load).format('lll')" sortable="'map_load'"
                                                    data-title="'{{ trans('tables.servers.show.columns.col1') }}'"></td>
                                                <td ng-bind="moment(map.round_start).format('lll')"
                                                    sortable="'round_start'"
                                                    data-title="'{{ trans('tables.servers.show.columns.col2') }}'"></td>
                                                <td ng-bind="moment(map.round_end).format('lll')" sortable="'round_end'"
                                                    data-title="'{{ trans('tables.servers.show.columns.col3') }}'"></td>
                                                <td ng-bind="map.map_name"
                                                    data-title="'{{ trans('tables.servers.show.columns.col4') }}'"></td>
                                                <td ng-bind="map.gamemode"
                                                    data-title="'{{ trans('tables.servers.show.columns.col5') }}'"></td>
                                                <td ng-bind="map.rounds"
                                                    data-title="'{{ trans('tables.servers.show.columns.col6') }}'"></td>
                                                <td ng-bind="map.players.min"
                                                    data-title="'{{ trans('tables.servers.show.columns.col7') }}'"></td>
                                                <td ng-bind="map.players.avg"
                                                    data-title="'{{ trans('tables.servers.show.columns.col8') }}'"></td>
                                                <td ng-bind="map.players.max"
                                                    data-title="'{{ trans('tables.servers.show.columns.col9') }}'"></td>
                                                <td ng-bind="map.players.join"
                                                    data-title="'{{ trans('tables.servers.show.columns.col10') }}'"></td>
                                                <td ng-bind="map.players.left"
                                                    data-title="'{{ trans('tables.servers.show.columns.col11') }}'"></td>
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
