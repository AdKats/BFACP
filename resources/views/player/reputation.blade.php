@extends('layout.main')

@section('content')
    <div class="row">
        @foreach($games as $game)
            <div class="col-xs-12 col-sm-6">
                <div class="box box-info">
                    <div class="box-header">
                        <h3 class="box-title">{{ $game->Name }}</h3>
                    </div>

                    <div class="box-body">
                        <table class="table table-striped table-condensed">
                            <thead>
                                <th>Player</th>
                                <th>Reputation</th>
                            </thead>

                            <tbody>
                                @foreach($game->reputations as $rep)
                                    <tr>
                                        <td>{{ $rep->player->SoldierName }}</td>
                                        <td>{{ $rep->total_rep_co }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@stop
