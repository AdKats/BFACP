@extends('layout.main')

@section('content')

    <div class="row" style="padding-bottom: 15px">
        <div class="col-xm-12 col-sm-12 col-md-12 col-lg-12">
            <select class="form-control" id="serversel" ng-model="serverSelect" ng-change="switchServer()" ng-init="serverSelect = 'none'">
                <option value="none" selected="selected">Select Server</option>
                @if(!empty($servers['bf3']) && Config::get('webadmin.BF3'))
                <optgroup label="Battlefield 3">
                    @foreach($servers['bf3'] as $server)
                    <option value="{{{ $server->ServerID }}}">{{{ $server->ServerName }}}</option>
                    @endforeach
                </optgroup>
                @endif
                @if(!empty($servers['bf4']) && Config::get('webadmin.BF4'))
                <optgroup label="Battlefield 4">
                    @foreach($servers['bf4'] as $server)
                    <option value="{{{ $server->ServerID }}}">{{{ $server->ServerName }}}</option>
                    @endforeach
                </optgroup>
                @endif
            </select>
        </div>
    </div>

    <div class="row">

        <scoreboard></scoreboard>

        <scoreboard-chat></scoreboard-chat>

    </div>
@stop

@section('jsinclude')
<script src="{{ asset('js/BFAdminCP/directives/ScoreboardDirective.js') }}"></script>
<script src="{{ asset('js/BFAdminCP/controllers/ScoreboardCtrl.js') }}"></script>
@stop

@section('modal_content')
    <div class="modal fade" id="online_admins" tabindex="-1" role="dialog" aria-labelledby="onlineAdmins" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Admins Currently In Server <span class="badge bg-light-blue" id="online_admins_total"></span></h4>
                </div>
                <div class="modal-body">
                    <ul class="list-unstyled" id="online_admins_list"></ul>
                </div>
            </div>
        </div>
    </div>
@stop
