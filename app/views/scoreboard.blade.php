@extends('layout.main')

@section('content')

<div ng-controller="ScoreboardController">
    <div class="row" style="padding-bottom: 10px">
        <div class="col-xs-12">
            {{ Former::select()
                ->options($servers)
                ->ng_model('selectedId')
                ->ng_change('switchServer()')
                ->ng_disabled('refresh')
            }}
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

    <div class="row" ng-if="alerts.length > 0">
        <div class="col-xs-12">
            <alert ng-repeat="alert in alerts" type="@{{ alert.type }}" close="closeAlert($index)">@{{ alert.msg }}</alert>
        </div>
    </div>

    @include('partials._scoreboard')
</div>

@stop

@section('header-scripts')
<script type="text/javascript">
    window.idleDurationSeconds = 60 * 30; // 30 minutes
</script>
@stop

@section('scripts')
{{ HTML::script('js/plugins/slimScroll/jquery.slimscroll.min.js') }}
<script type="text/javascript">$('#chat-box').slimScroll({height: '300px'});</script>
<script type="text/ng-template" id="warning-dialog.html">
  <div class="modal-header">
      <h3>You're Idle.</h3>
  </div>
  <div class="modal-body" ng-idle-countdown="countdown" ng-init="countdown = 60">
      <p>Scoreboard will be disabled in <span class="label label-danger">@{{countdown}}</span> <span ng-pluralize="" count="countdown" when="{'one': 'second', 'other': 'seconds' }"></span>.</p>
      <progressbar max="60" value="countdown" animate="true" class="progress-striped active" type="warning"></progressbar>
  </div>

</script>
<script type="text/ng-template" id="timedout-dialog.html">
  <div class="modal-header">
      <h3>You've Timed Out!</h3>
  </div>
  <div class="modal-body">
      <p>
        You were idle too long. The scoreboard has been disabled to save bandwidth.
      </p>
  </div>
</script>
@stop
