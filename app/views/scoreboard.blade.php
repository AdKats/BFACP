@extends('layout.main')

@section('content')

<div ng-controller="ScoreboardController">
    <div class="row" style="padding-bottom: 10px">
        <div class="col-xs-12">
            <select class="form-control" ng-model="selectedId" ng-change="switchServer()" ng-disabled="refresh">
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

    <div class="row" ng-if="requestError">
        <div class="col-xs-12">
            <alert type="error">
                <strong>Fatal Error!</strong>
                The request couldn't complete due to a server side error. Please choose another server or <a href="javascript://" ng-click="switchServer()">retry</a>. Notify an administrator if this error persists.
            </alert>
        </div>
    </div>

    @include('partials._scoreboard')
</div>

@stop
