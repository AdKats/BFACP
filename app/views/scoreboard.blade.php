@extends('layout.main')

@section('content')

<div ng-controller="ScoreboardController">
    <div class="row" style="padding-bottom: 10px">
        <div class="col-xs-12">
            <select class="form-control" ng-model="selectedId" ng-change="switchServer()">
                <option value="-1" selected>Select Server...</option>
                @foreach($games as $game)
                <optgroup label="{{ $game->Name }}">
                    @foreach($game->servers as $server)
                    <option value="{{ $server->ServerID }}">{{{ $server->ServerName }}}</option>
                    @endforeach
                </optgroup>
                @endforeach
            </select>
        </div>
    </div>

    @include('partials._scoreboard')
</div>

@stop
