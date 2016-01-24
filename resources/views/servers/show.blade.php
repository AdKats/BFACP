@extends('layout.main')

@section('content')
    <div class="row" ng-controller="ServerController">
        <div class="col-xs-12">
            <div class="box box-solid">
                <div class="box-header">
                    <h3 class="box-title">
                        <span class="{{ $server->game->class_css }}">{{ $server->game->Name }}</span>
                        {{ $server->server_name_short or $server->ServerName }}
                    </h3>

                    <div ng-if="loading" class="box-tools pull-right animate-if" ng-cloak>
                        <i class="fa fa-cog fa-lg fa-spin"></i><strong> Loading...</strong>
                    </div>
                </div>

                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12" id="population-history"></div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12" id="popular-maps"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="server_id" value="{{ $server->ServerID }}">
@stop
