@extends('layout.main')

@section('styles')
{{ HTML::style('css/jvectormap/jquery-jvectormap-1.2.2.css') }}
@stop

@section('content')
<div ng-controller="DashboardController">

    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon" ng-class="populationColor(results.population.percentage, true)"><i class="ion ion-ios-people-outline"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ Lang::get('dashboard.metro.players_online') }}</span>
                    <span class="info-box-number">
                        <span count-to="@{{ results.population.online }}" value="@{{ results.population.old.online }}" duration="1"></span>&nbsp;/
                        <span count-to="@{{ results.population.total }}" value="@{{ results.population.old.total }}" duration="1"></span>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="ion ion-stats-bars"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ Lang::get('dashboard.metro.player_count') }}</span>
                    <span class="info-box-number">
                        <span count-to="{{ $uniquePlayers }}" value="0" duration="3"></span>
                    </span>
                </div>
            </div>
        </div>

        <div class="clearfix visible-sm-block"></div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="ion ion-hammer"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ Lang::get('dashboard.metro.average_bans') }}</span>
                    <span class="info-box-number">
                        <span count-to="@{{ results.banstats.average }}" value="0" duration="1"></span>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="ion ion-hammer"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ Lang::get('dashboard.metro.yesterday_bans') }}</span>
                    <span class="info-box-number">
                        <span count-to="@{{ results.banstats.yesterday }}" value="0" duration="1"></span>
                    </span>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="cold-xs-12 col-md-6 col-lg-3">
            <div class="info-box bg-navy disabled">
                <span class="info-box-icon"><i class="fa fa-bed"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ Lang::get('dashboard.metro.adkats.titles.killed') }}</span>
                    <span class="info-box-number">{{ number_format($adkats_statistics->PercentageKilled) }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: {{ MainHelper::percent($adkats_statistics->PercentageKilled, $uniquePlayers) }}%"></div>
                    </div>
                    <span class="progress-description">
                    {{ Lang::get('dashboard.metro.adkats.killed', [
                        'killed' => MainHelper::percent($adkats_statistics->PercentageKilled, $uniquePlayers)
                    ]) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="cold-xs-12 col-md-6 col-lg-3">
            <div class="info-box bg-navy disabled">
                <span class="info-box-icon"><i class="fa fa-trash"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ Lang::get('dashboard.metro.adkats.titles.kicked') }}</span>
                    <span class="info-box-number">{{ number_format($adkats_statistics->PercentageKicked) }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: {{ MainHelper::percent($adkats_statistics->PercentageKicked, $uniquePlayers) }}%"></div>
                    </div>
                    <span class="progress-description">
                    {{ Lang::get('dashboard.metro.adkats.kicked', [
                        'kicked' => MainHelper::percent($adkats_statistics->PercentageKicked, $uniquePlayers)
                    ]) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="cold-xs-12 col-md-6 col-lg-3">
            <div class="info-box bg-navy disabled">
                <span class="info-box-icon"><i class="ion ion-hammer"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ Lang::get('dashboard.metro.adkats.titles.banned') }}</span>
                    <span class="info-box-number">{{ number_format($adkats_statistics->PercentageBanned) }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: {{ MainHelper::percent($adkats_statistics->PercentageBanned, $uniquePlayers) }}%"></div>
                    </div>
                    <span class="progress-description">
                    {{ Lang::get('dashboard.metro.adkats.banned', [
                        'banned' => MainHelper::percent($adkats_statistics->PercentageBanned, $uniquePlayers)
                    ]) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="cold-xs-12 col-md-6 col-lg-3">
            <div class="info-box bg-navy disabled">
                <span class="info-box-icon"><i class="fa fa-frown-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ Lang::get('dashboard.metro.adkats.titles.banned_active') }}</span>
                    <span class="info-box-number">{{ number_format($adkats_statistics->PercentageBanned_Active) }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: {{ MainHelper::percent($adkats_statistics->PercentageBanned_Active, $uniquePlayers) }}%"></div>
                    </div>
                    <span class="progress-description">
                    {{ Lang::get('dashboard.metro.adkats.banned_active', [
                        'banned' => MainHelper::percent($adkats_statistics->PercentageBanned_Active, $uniquePlayers)
                    ]) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-lg-6" ng-include="'js/templates/serverpopulation.html'" onload="population()"></div>
        <div class="col-xs-12 col-lg-6">
            <div class="box box-solid bg-light-blue-gradient">
                <div class="box-header">
                    <i class="fa fa-map-marker"></i>
                    <h3 class="box-title">
                        {{ Lang::get('dashboard.players_seen_country_past_day') }}
                    </h3>
                </div>

                <div class="box-body">
                    <div id="player-world-map" style="height: 350px"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-md-7 col-lg-6">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ Lang::get('dashboard.bans.title') }}</h3>
                    <div class="box-tools pull-right">
                        @if($user->isLoggedIn)
                        <label>
                            Display Only My Bans
                            <input type="checkbox" ng-model="opts.bans.personal" ng-change="latestBans()">
                        </label>
                        @endif
                        <button class="btn btn-box-tool" ng-click="latestBans()" tooltip="Refresh" id="latest-ban-refresh-btn"><i class="fa fa-refresh"></i></button>
                    </div>
                </div>

                <div class="box-body" ng-include="'js/templates/latestbans.html'" onload="latestBans()"></div>

                <div class="overlay" ng-if="!loaded.bans">
                    <i class="fa fa-refresh fa-spin"></i>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-md-5 col-lg-6" ng-include="'js/templates/metabans.html'" onload="metabans()"></div>
    </div>
</div>
@stop

@section('scripts')
{{ HTML::script('js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') }}
{{ HTML::script('js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}
<script type="text/javascript">
$(function() {
    var playerVisitorData = {{ json_encode($countryMap, TRUE) }};

    $('#player-world-map').vectorMap({
        map: 'world_mill_en',
        backgroundColor: "transparent",
        regionStyle: {
            initial: {
                fill: '#e4e4e4',
                "fill-opacity": 1,
                stroke: 'none',
                "stroke-width": 0,
                "stroke-opacity": 1
            }
        },
        series: {
            regions: [{
                values: playerVisitorData,
                scale: ["#ebf4f9", "#92c1dc"],
                normalizeFunction: 'polynomial'
            }]
        },
        onRegionLabelShow: function(e, el, code) {
            if (typeof playerVisitorData[code] != "undefined")
                el.html(el.html() + ': ' + playerVisitorData[code].toLocaleString() + ' players');
        }
    });
});
</script>
@stop
