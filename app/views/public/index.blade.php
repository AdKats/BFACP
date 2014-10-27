@section('content')

@if(Config::get('webadmin.MOTD') && Entrust::can('view_motd'))
<div class="row">
    <section class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">{{ Lang::get('dashboard.motd') }}</h3>
            </div>

            <div class="box-body">
                {{ Config::get('webadmin.MOTD-TXT') }}
            </div>
        </div>
    </section>
</div>
@endif

<div class="row">
    <div class="col-lg-3 col-xs-4">
        <div class="small-box bg-blue">
            <div class="inner">
                <h3>
                    {{ number_format($stats['bans']) }}
                </h3>
                <br>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <div class="small-box-footer">{{ Lang::choice('dashboard.bans_issued_yesterday', $stats['bans']) }}</div>
        </div>
    </div>
    <div class="col-lg-3 col-xs-4">
        <div class="small-box bg-light-blue">
            <div class="inner">
                <h3>
                    ~{{ number_format($stats['bansPerDay']->AvgBansPerDay) }}
                </h3>
                <br>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <div class="small-box-footer">{{ Lang::get('dashboard.bans_avg_per_day') }}</div>
        </div>
    </div>
    <div class="col-lg-3 col-xs-4">
        <div class="small-box bg-green">
            <div class="inner">
                <h3>
                    {{ number_format($stats['players']) }}
                </h3>
                <br>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <div class="small-box-footer">{{ Lang::choice('dashboard.players_online', $stats['players']) }}</div>
        </div>
    </div>
</div>

<div class="row">
    @include('angular.public.dashboard-population')

    @if( Helper::_empty(Config::get('webadmin.MB-KEY')) || Helper::_empty(Config::get('webadmin.MB-USR')) || Helper::_empty(Config::get('webadmin.MB-ACC') ) )
        @if(Entrust::can('manage_site_settings'))
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <div class="alert alert-danger">Metabans configuration not set up</div>
        </div>
        @endif
    @else
        @include('angular.public.dashboard-metabans')
    @endif

</div>

<div class="row">
    <section class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        @if(Config::get('webadmin.BF3'))
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">{{ Lang::get('dashboard.banfeed.title', ['game' => 'BF3']) }}</h3>
            </div>

            <div class="box-body">
                @if(empty($bans['bf3']))
                <div class="alert alert-success">
                    <b>Awesome!</b> No recent bans have been filed.
                </div>
                @else
                    @include('subviews.dashboard_recent_ban_list', ['bans' => $bans['bf3']])
                @endif
            </div>
        </div>
        @endif
    </section>

    <section class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        @if(Config::get('webadmin.BF4'))
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">{{ Lang::get('dashboard.banfeed.title', ['game' => 'BF4']) }}</h3>
            </div>

            <div class="box-body">
                @if(empty($bans['bf4']))
                <div class="alert alert-success">
                    <b>Awesome!</b> No recent bans have been filed.
                </div>
                @else
                    @include('subviews.dashboard_recent_ban_list', ['bans' => $bans['bf4']])
                @endif
            </div>
        </div>
        @endif
    </section>
</div>

@stop

@section('jsinclude')
<script src="{{ asset('js/BFAdminCP/controllers/PopulationFeedCtrl.js') }}"></script>
<script src="{{ asset('js/BFAdminCP/controllers/MetabansFeedCtrl.js') }}"></script>
@stop
