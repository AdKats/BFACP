@extends('layout.main')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <form method="get" action="<?php echo $_SERVER['REQUEST_URI'];?>" class="form-inline">
                <div class="form-group">
                    <select class="form-control" name="sid" id="sid">
                        @foreach($serverlisting as $server)
                        <option value="{{$server->ServerID}}" {{ ($server->ServerID == Input::get('sid') ? 'selected' : NULL) }}>{{ $server->ServerName }}</option>
                        @endforeach
                    </select>
                </div>

                <input type="submit" class="btn btn-default" value="Show Stats">
            </form>
        </div>
    </div>

    @if(Input::has('sid'))

    <div class="page-header">
        <h1><small>{{ $results['serverinfo']->ServerName }}</small></h1>
    </div>

    <div class="row">

        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title">Server Stats</h3>
                </div>

                <div class="box-body">
                    @if(empty($results['stats']['server']))
                    <div class="alert alert-warning">
                        <i class="fa fa-warning"></i>
                        <b>Warning!</b> Missing required information to display server stats.
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-condensed">
                            <thead>
                                <th>Players</th>
                                <th>Total Score</th>
                                <th>Total Kills</th>
                                <th>Total Headshots</th>
                                <th>Total Deaths</th>
                                <th>Total Suicides</th>
                                <th>Total TKs</th>
                                <th>Rounds Played</th>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>{{ number_format($results['stats']['server']->CountPlayers) }}</td>
                                    <td>{{ number_format($results['stats']['server']->SumScore) }}</td>
                                    <td>{{ number_format($results['stats']['server']->SumKills) }}</td>
                                    <td>{{ number_format($results['stats']['server']->SumHeadshots) }}</td>
                                    <td>{{ number_format($results['stats']['server']->SumDeaths) }}</td>
                                    <td>{{ number_format($results['stats']['server']->SumSuicide) }}</td>
                                    <td>{{ number_format($results['stats']['server']->SumTKs) }}</td>
                                    <td>{{ number_format($results['stats']['server']->SumRounds) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title">Population History</h3>
                </div>

                <div class="box-body">
                    <div id="population-graph"></div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title">Round Stats</h3>
                    <div class="box-tools pull-right">
                        <select class="form-control" id="round_stats_select">
                            @forelse($roundstats as $key => $stat)
                            <option value="{{ $stat->round_id }}" {{ ($key == 0 ? 'selected' : '') }}>{{ Helper::UTCToLocal($stat->RoundStart, $_tz)->format('M j, Y g:ia T') }} - {{ Helper::UTCToLocal($stat->RoundEnd, $_tz)->format('M j, Y g:ia T') }}</option>
                            @empty
                            <option value="none" selected>No Round Stats Collected</option>
                            @endforelse
                        </select>
                    </div>
                </div>

                <div class="box-body">
                    <div id="roundstats-graph"></div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            @if(array_key_exists('error', $results['stats']['uptime']))
            <div class="box box-danger">
                <div class="box-header">
                    <h3 class="box-title">Uptime History</h3>
                </div>

                <div class="box-body">
                    <div class="alert alert-danger">
                        <b>Error!</b> An error occured when querying server.<br><br>
                        {{ $results['stats']['uptime']['error'] }}
                    </div>
                </div>
            </div>
            @else
            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title">Uptime History</h3>
                </div>

                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div id="ping-graph"></div>
                        </div>

                        <div class="col-sm-12">
                            <table class="table table-condensed">
                                <thead>
                                    <th width="80px">Event</th>
                                    <th width="80px">Duration</th>
                                    <th>Timestamp</th>
                                </thead>

                                <tbody>
                                    @foreach($results['stats']['uptime']['logs'] as $log)
                                    <tr>
                                        <td>
                                            @if($log['type'] == 'up')
                                            <span class="label label-success"><i class="fa fa-long-arrow-up"></i> Up</span>
                                            @elseif($log['type'] == 'down')
                                            <span class="label label-danger"><i class="fa fa-long-arrow-down"></i> Down</span>
                                            @endif
                                        </td>
                                        <td>{{ $log['duration'] or 'N/A' }}</td>
                                        <td>{{ Carbon::createFromFormat('m/d/y H:i:s', $log['timestamp'])->toDayDateTimeString() }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title">Map stats for the last 72 hours</h3>
                </div>

                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-condensed" id="mapstats-table">
                            <thead>
                                <th>ID</th>
                                <th>Loaded</th>
                                <th>Round Start</th>
                                <th>Round End</th>
                                <th>Map</th>
                                <th>Gamemode</th>
                                <th>Min Players</th>
                                <th>Avg Players</th>
                                <th>Max Players</th>
                                <th>Joined</th>
                                <th>Left</th>
                            </thead>

                            <tbody>
                                @foreach($results['stats']['maps'] as $stat)
                                <tr>
                                    <td>{{ $stat->ID }}</td>
                                    <td>{{ $stat->TimeMapLoad }}</td>
                                    <td>{{ $stat->TimeRoundStarted }}</td>
                                    <td>{{ $stat->TimeRoundEnd }}</td>
                                    <td>{{ $stat->MapName }}</td>
                                    <td>{{ $stat->Gamemode }}</td>
                                    <td>{{ $stat->MinPlayers }}</td>
                                    <td>{{ $stat->AvgPlayers }}</td>
                                    <td>{{ $stat->MaxPlayers }}</td>
                                    <td>{{ $stat->PlayersJoinedServer }}</td>
                                    <td>{{ $stat->PlayersLeftServer }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title">Favorite maps for the past week</h3>
                </div>

                <div class="box-body">
                    <div id="maps_pie_chart"></div>
                </div>
            </div>
        </div>

    </div>

    @endif
@stop

@section('javascript')
@if(Input::has('sid'))
<script type="text/javascript">
$(function() {
    $('#mapstats-table').dataTable({
        "aaSorting": [[ 0, "desc" ]]
    });

    $('#maps_pie_chart').highcharts({
        title: {
            text: ''
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Map share',
            data: <?php echo json_encode($results['stats']['maps_pie']); ?>
        }]
    });

    $('#ping-graph').highcharts({
        chart: {
            type: 'area',
            zoomType: 'x'
        },
        title: {
            text: 'Ping times for the last 24 hours'
        },
        subtitle: {
            text: ''
        },
        credits:{enabled:false},
        xAxis: {
            type: 'datetime',
            dateTimeLabelFormats: {
                hour: '%l:%M %p',
                month: '%b %e',
                year: '%b'
            },
            title: {
                text: 'Date'
            }
        },
        yAxis: {
            title: {
                text: ''
            },
            labels: {
                formatter: function() {
                    if(this.value < 1000) {
                        return this.value + ' ms';
                    }
                    else {
                        return (this.value / 1000) + ' secs';
                    }
                }
            },
            min: 0
        },
        tooltip: {
            pointFormat: '{point.y:.0f} ms'
        },
        series: [{
            name: 'Milliseconds',
            showInLegend: false,
            data: <?php echo json_encode($results['stats']['uptime']['ms']); ?>
        }]
    });

    $('#population-graph').highcharts({
        chart: {
            type: 'area',
            zoomType: 'x'
        },
        title: {
            text: 'Population for the past month'
        },
        subtitle: {
            text: ''
        },
        credits:{enabled:false},
        xAxis: {
            type: 'datetime',
            dateTimeLabelFormats: {
                hour: '%l:%M %p',
                month: '%b %e',
                year: '%b'
            },
            title: {
                text: 'Date'
            }
        },
        yAxis: {
            title: {
                text: 'Players'
            },
            labels: {
                formatter: function() {
                    return this.value;
                }
            },
            min: 0
        },
        tooltip: {
            pointFormat: '{point.y} player(s)'
        },
        series: [{
            name: 'Population',
            showInLegend: false,
            data: <?php echo json_encode($results['stats']['population']); ?>
        }]
    });

    <?php if(count($roundstats) > 0) : ?>
    var roundstatsoptions = {
        chart: {
            renderTo: 'roundstats-graph',
            zoomType: 'x'
        },
        title: {
            text: ''
        },
        xAxis: {
            type: 'datetime',
            dateTimeLabelFormats: {
                second: '%b %e %l:%M:%S %p',
                minute: '%b %e %l:%M %p',
                hour: '%b %e %l:%M %p',
                month: '%b %e',
                year: '%b'
            },
            title: {
                text: 'Date'
            },
            labels: {
                align: 'right',
                rotation: -30
            }
        },
        yAxis: {
            title: {
                text: 'Tickets'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }],
            min: 0,
            minRange: 0.1
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        plotOptions: {
            series: {
                marker: {
                    enabled: false
                }
            }
        },
        series: []
    };
    var roundstatschart;

    $.post("/api/v1/common/general/roundstats", {server_id: <?php echo $results['serverinfo']->ServerID; ?>, round_id: $("#round_stats_select option:selected").val()}, function(data)
    {
        roundstatsoptions.series = data.data;
        roundstatschart = new Highcharts.Chart(roundstatsoptions);
    });

    $("#round_stats_select").change(function() {
        console.log($(this).val());
        $.post("/api/v1/common/general/roundstats", {server_id: <?php echo $results['serverinfo']->ServerID; ?>, round_id: $(this).val()}, function(data)
        {
            roundstatsoptions.series = data.data;
            roundstatschart = new Highcharts.Chart(roundstatsoptions);
        });
    });
    <?php endif; ?>
});
</script>
@endif
@stop

@section('jsinclude')
<script src="{{ asset('js/plugins/datatables/jquery.dataTables.js') }}"></script>
<script src="{{ asset('js/plugins/datatables/dataTables.bootstrap.js') }}"></script>
@stop

@section('stylesinclude')
<link href="{{ asset('css/datatables/dataTables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
@stop
