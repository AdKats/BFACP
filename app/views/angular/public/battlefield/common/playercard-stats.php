<div class="col-xs-12" ng-controller="PlayerInfoStats">
    <div class="alert alert-info" ng-show="isLoading">
        <i class="icon ion-loading-c"></i>
        Loading stats... Please wait
    </div>

    <div class="row" ng-hide="isLoading">
        <div class="col-xs-12" ng-hide="overview.length == 0">
            <div class="box box-info">
                <div class="box-header">
                    <div class="box-title">Stats Summary</div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-condensed">
                            <thead>
                                <th>Score</th>
                                <th>Kills</th>
                                <th>Deaths</th>
                                <th>Headshots</th>
                                <th>Suicide</th>
                                <th>TKs</th>
                                <th>Wins</th>
                                <th>Losses</th>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>{{ overview.Score | number:0 }}</td>
                                    <td>{{ overview.Kills | number:0 }}</td>
                                    <td>{{ overview.Deaths | number:0 }}</td>
                                    <td>{{ overview.Headshots | number:0 }}</td>
                                    <td>{{ overview.Suicide | number:0 }}</td>
                                    <td>{{ overview.TKs | number:0 }}</td>
                                    <td>{{ overview.Wins | number:0 }}</td>
                                    <td>{{ overview.Losses | number:0 }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-12" ng-hide="sessions.length == 0">
            <div class="box box-info">
                <div class="box-header">
                    <div class="box-title">Session History</div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-condensed" id="session_stats">
                            <thead>
                                <th>ID</th>
                                <th width="180px">Joined</th>
                                <th width="180px">Left</th>
                                <th>Sum Score</th>
                                <th>Highest Score</th>
                                <th>Kills</th>
                                <th>Deaths</th>
                                <th>Headshots</th>
                                <th>Suicides</th>
                                <th>TKs</th>
                                <th>Wins</th>
                                <th>Losses</th>
                                <th>Rounds</th>
                                <th width="100px">Playtime</th>
                                <th width="250px">Server</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
