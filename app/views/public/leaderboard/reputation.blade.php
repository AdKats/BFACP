@extends('layout.main')

@section('content')
<div class="row">
    @if(Config::get('webadmin.BF3'))

    @if(Config::get('webadmin.BF3') && Config::get('webadmin.BF4'))
    <div class="col-xs-6">
    @else
    <div class="col-xs-12">
    @endif
        <div class="box box-info">
            <div class="box-header">
                <h3 class="box-title">BF3 Top 100</h3>
            </div>

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <thead>
                            <th width="20px">#</th>
                            <th>Player</th>
                            <th>Points</th>
                        </thead>

                        <tbody>
                            @foreach($_bf3top100 as $key => $lb)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ link_to_action('ADKGamers\\Webadmin\\Controllers\\PlayerController@showInfo', $lb->SoldierName, [$lb->PlayerID, $lb->SoldierName]) }}</td>
                                <td>{{ number_format($lb->total_rep_co, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(Config::get('webadmin.BF4'))

    @if(Config::get('webadmin.BF3') && Config::get('webadmin.BF4'))
    <div class="col-xs-6">
    @else
    <div class="col-xs-12">
    @endif
        <div class="box box-info">
            <div class="box-header">
                <h3 class="box-title">BF4 Top 100</h3>
            </div>

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <thead>
                            <th width="20px">#</th>
                            <th>Player</th>
                            <th>Points</th>
                        </thead>

                        <tbody>
                            @foreach($_bf4top100 as $key => $lb)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ link_to_action('ADKGamers\\Webadmin\\Controllers\\PlayerController@showInfo', $lb->SoldierName, [$lb->PlayerID, $lb->SoldierName]) }}</td>
                                <td>{{ number_format($lb->total_rep_co, 2) }}</td>
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
@stop
