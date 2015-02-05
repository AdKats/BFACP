@extends('layout.main')

@section('content')
<div ng-controller="DashboardController">

    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon" ng-class="populationColor(results.population.percentage, true)"><i class="ion ion-ios-people-outline"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ Lang::get('dashboard.metro.players_online') }}</span>
                    <span class="info-box-number">
                        <span count-to="@{{ results.population.online }}" value="@{{ results.population.old.online }}" duration="3"></span>&nbsp;/
                        <span count-to="@{{ results.population.total }}" value="@{{ results.population.old.total }}" duration="3"></span>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="ion ion-stats-bars"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ Lang::get('dashboard.metro.player_count') }}</span>
                    <span class="info-box-number">
                        <span count-to="{{ $uniquePlayers }}" value="0" duration="4"></span>
                    </span>
                </div>
            </div>
        </div>

        <div class="clearfix visible-sm-block"></div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="ion ion-stats-bars"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ Lang::get('dashboard.metro.average_bans') }}</span>
                    <span class="info-box-number">
                        <span count-to="@{{ results.banstats.average }}" value="0" duration="3"></span>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="ion ion-stats-bars"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ Lang::get('dashboard.metro.yesterday_bans') }}</span>
                    <span class="info-box-number">
                        <span count-to="@{{ results.banstats.yesterday }}" value="0" duration="3"></span>
                    </span>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-xs-12 col-lg-6" ng-include="'js/templates/serverpopulation.html'"></div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-lg-6">
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

                <div class="box-body" ng-include="'js/templates/latestbans.html'"></div>

                <div class="overlay" ng-if="!loaded.bans">
                    <i class="fa fa-refresh fa-spin"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
