@extends('layout.main')

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-warning">
            <i class="fa fa-warning"></i>
            <b>Notice!</b> The stats reflected here are provided by XpKiller Stats Plugin for Procon Frostbite. These do not reflect the players real stats from Battlelog.
        </div>
    </div>

    @if(Config::get('webadmin.BF3'))
    <div class="col-xs-12">
        @include('subviews.leaderboard_pstats', ['stats' => $_bf3stats, 'block_title' => 'BF3 Top 50'])
    </div>
    @endif

    @if(Config::get('webadmin.BF4'))
    <div class="col-xs-12">
        @include('subviews.leaderboard_pstats', ['stats' => $_bf4stats, 'block_title' => 'BF4 Top 50'])
    </div>
    @endif
</div>
@stop
