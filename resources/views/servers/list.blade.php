@extends('layout.main')

@section('content')
    @foreach($servers as $server)
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-header">
                        <h3 class="box-title">
                            <span class="{{ $server->game->class_css }}">{{ $server->game->Name }}</span>
                            {{ link_to_route('servers.show', ! is_null($server->server_name_short) ? $server->server_name_short : $server->ServerName, [$server->ServerID, $server->slug]) }}
                        </h3>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">
                                {!! Html::image($server->map_image_paths['wide'], $server->current_map, ['class' => 'center-img img-responsive img-rounded', 'title' => $server->current_map]) !!}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <th>{{ trans('tables.serverlist.columns.col1') }}</th>
                                            <th>{{ trans('tables.serverlist.columns.col2') }}</th>
                                            <th>{{ trans('tables.serverlist.columns.col3') }}</th>
                                            <th>{{ trans('tables.serverlist.columns.col4') }}</th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <label>
                                                        <span class="text-blue">{{ $server->usedSlots }} / {{ $server->maxSlots }} ({{ $server->in_queue }})</span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label>
                                                        <span class="text-blue">{{ $server->current_map }}</span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label>
                                                        <span class="text-blue">{{ $server->current_gamemode }}</span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label>
                                                        <span class="text-blue">{{ $server->usedSlots }} / {{ $server->maxSlots }} ({{ $server->in_queue }})</span>
                                                        <span class="text-blue">{{ Macros::moment(null, $server->stats->SumPlaytime) }}</span>
                                                    </label>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-3 col-sm-6 col-xs-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-green"><i class="ion ion-stats-bars"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Players Joined</span>
                                        <span class="info-box-number" ng-bind="{{ $server->stats->CountPlayers }} | number"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-xs-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-green"><i class="ion ion-stats-bars"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Kills</span>
                                        <span class="info-box-number" ng-bind="{{ $server->stats->SumKills }} | number"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix visible-sm-block"></div>

                            <div class="col-lg-3 col-sm-6 col-xs-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-green"><i class="ion ion-stats-bars"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Rounds Played</span>
                                        <span class="info-box-number" ng-bind="{{ $server->stats->SumRounds }} | number">/span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-xs-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-green"><i class="ion ion-stats-bars"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Average Score</span>
                                        <span class="info-box-number" ng-bind="{{ (int) $server->stats->AvgScore }} | number">/span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@stop
