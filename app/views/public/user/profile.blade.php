@extends('layout.main')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_profile" data-toggle="tab">Overview</a></li>
                <li><a href="#tab_timeline" data-toggle="tab">Timeline</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="tab_profile">
                    <div class="row">
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                            <p><img src="{{ Helper::getGravatar(($user->preferences->gravatar ?: $user->email), 120) }}" class="img-thumbnail" alt="{{ $user->username }} Avatar" /></p>
                            <dl class="dl-horizontal">
                                <dt>Member Group</dt>
                                <dd>{{ $user->group() }}</dd>
                                <dt>BF3 Player</dt>
                                <dd>
                                    @if(!empty($_pids['bf3']))
                                    {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\PlayerController@showInfo', $_pids['bf3']->SoldierName, [$_pids['bf3']->PlayerID, $_pids['bf3']->SoldierName]) }}
                                    @else
                                    N/A
                                    @endif
                                </dd>
                                <dt>BF4 Player</dt>
                                <dd>
                                    @if(!empty($_pids['bf4']))
                                    {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\PlayerController@showInfo', $_pids['bf4']->SoldierName, [$_pids['bf4']->PlayerID, $_pids['bf4']->SoldierName]) }}
                                    @else
                                    N/A
                                    @endif
                                </dd>
                                <dt>Member Since</dt>
                                <dd>{{ Helper::UTCToLocal($user->created_at)->format('M j, Y') }}</dd>
                                <dt>Last Active</dt>
                                <dd>
                                    <?php $diff = Carbon::parse($user->lastseen_at)->diffInMinutes(Carbon::now(), FALSE); ?>
                                    @if($diff <= 15 && $diff >= 0)
                                    <small class="label label-success">ONLINE</small>
                                    @else
                                    <small class="label label-danger">OFFLINE</small>
                                    @endif
                                    {{ Helper::UTCToLocal($user->lastseen_at)->diffForHumans() }}
                                </dd>
                            </dl>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <div id="overall-cmd-history"></div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tab_timeline">
                    <div class="row">
                        <div class="col-lg-12">
                            @if(empty($timeline))
                            <div class="alert alert-info alert-dismissable">
                                <i class="fa fa-info"></i>
                                There has been no activity for the past 3 months for {{{ $user->username }}}.
                            </div>
                            @else
                            <ul class="timeline">
                                @foreach($timeline as $tevent)
                                    <li class="time-label">
                                        <span class="bg-gray">
                                            {{ date('j M Y', strtotime($tevent['date'])) }}
                                            &nbsp;<span class="badge bg-purple">{{ count($tevent['data']) }}</span>
                                        </span>
                                    </li>
                                    @foreach($tevent['data'] as $e)
                                    <li>
                                        <i class="fa {{ ($e['target_id'] == $e['source_id'] ? 'fa-terminal bg-blue' : 'fa-legal bg-red') }}"></i>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fa fa-clock-o"></i> {{ date('g:i:s a', strtotime($e['record_time'])) }}</span>
                                            <h3 class="timeline-header">{{ $servers[$e['server_id']] }}</h3>
                                            <div class="timeline-body">
                                                Issued
                                                <strong>
                                                {{ $commands[$e['command_type']] }}
                                                @if($e['command_type'] == 9)
                                                ({{ $commands[$e['command_action']] }})
                                                @endif
                                                </strong>

                                                @if($e['command_action'] == 7)
                                                    (<strong>{{ Helper::convertSecToStr($e['command_numeric'], true) }}</strong>)
                                                @endif

                                                on <strong>{{ link_to_action('ADKGamers\\Webadmin\\Controllers\\PlayerController@showInfo', $e['target_name'], [$e['target_id'], $e['target_name']]) }}</strong> for <strong>{{ $e['record_message'] }}</strong>
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                @endforeach
                            </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop
