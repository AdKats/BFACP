@extends('layout.main')

@section('content')
    @foreach($servers as $server)
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-header">
                        <h3 class="box-title">
                            <span class="{{ $server->game->class_css }}">{{ $server->game->Name }}</span>
                            {{ link_to_route('servers.show', $server->ServerName, [$server->ServerID, $server->slug]) }}
                        </h3>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">
                                {{ HTML::image($server->map_image_paths['wide'], $server->current_map, ['class' => 'center-img']) }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 col-xs-4">
                                <label>
                                    Total Playtime: <span class="text-blue">{{ HTML::moment(null, $server->stats->SumPlaytime) }}</span>
                                </label>
                            </div>
                            <div class="col-md-4 col-xs-4">
                                <label>
                                    Current Map: <span class="text-blue">{{ $server->current_map }}</span>
                                </label>
                            </div>
                            <div class="col-md-4 col-xs-4">
                                <label>
                                    Current Gamemode: <span class="text-blue">{{ $server->current_gamemode }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-3 col-sm-6 col-xs-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-green"><i class="ion ion-stats-bars"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Players Joined</span>
                                        <span class="info-box-number">
                                            <span count-to="{{ $server->stats->CountPlayers }}" value="0" duration="5"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix visible-sm-block"></div>

                            <div class="col-lg-3 col-sm-6 col-xs-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-green"><i class="ion ion-stats-bars"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Kills</span>
                                        <span class="info-box-number">
                                            <span count-to="{{ $server->stats->SumKills }}" value="0" duration="5"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix visible-sm-block"></div>

                            <div class="col-lg-3 col-sm-6 col-xs-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-green"><i class="ion ion-stats-bars"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Rounds Played</span>
                                        <span class="info-box-number">
                                            <span count-to="{{ $server->stats->SumRounds }}" value="0" duration="5"></span>
                                        </span>
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