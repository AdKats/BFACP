<li class="dropdown messages-menu" ng-controller="ReportsCheckCtrl">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" ng-click="markRead()">
        <i class="fa fa-envelope"></i>
        <span class="label label-success" ng-hide="hasRead">{{countNew}}</span>
    </a>
    <ul class="dropdown-menu" style="width:380px">
        <li class="header">Report Notifications <div class="pull-right"><i class="fa fa-refresh fa-spin" ng-show="refresh"></i></div></li>
        <li>
            <!-- inner menu: contains the actual data -->
            <ul class="menu">
                <li ng-repeat="report in reports | orderBy:'-record_id' track by report.record_id"><!-- start message -->
                    <a ng-href="/player/{{report.target_id}}/{{report.target}}">
                        <div class="pull-left">
                            <i class="fa fa-check fa-2x text-green" ng-if="report.action_id==19"></i>
                            <i class="fa fa-times fa-2x text-red" ng-if="report.action_id==42"></i>
                            <i class="fa fa-exclamation-triangle fa-2x text-blue" ng-if="report.action_id==18 || report.action_id==20"></i>
                            <i class="fa fa-exclamation-triangle fa-2x" ng-if="report.action_id==62"></i>
                        </div>
                        <h4>
                            [{{report.id}}] {{report.target}}
                            <small><i class="fa fa-clock-o"></i> <span data-livestamp="{{report.timestamp}}"></span></small>
                        </h4>
                        <p>{{report.message}}</p>
                        <p><small>{{displayServerName(report.server)}}</small></p>
                    </a>
                </li><!-- end message -->
            </ul>
        </li>
    </ul>
</li>
