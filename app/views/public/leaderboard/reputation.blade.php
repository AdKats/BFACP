@extends('layout.main')

@section('content')
<div class="row">
    <div class="col-xs-6">
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

    <div class="col-xs-6">
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
</div>
@stop
