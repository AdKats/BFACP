<div class="col-xs-12" ng-controller="PlayerInfoStats">
    <div class="alert alert-info" ng-show="isLoading">
        <i class="icon ion-loading-c"></i>
        Loading stats... Please wait
    </div>

    <div class="row" ng-hide="isLoading">
        <div class="col-xs-12" ng-hide="overview.length == 0">
            <div class="box box-info">
                <div class="box-header">
                    <div class="box-title"><?php echo Lang::get('player.profile.section_titles.stats_summary'); ?></div>
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

        <div class="col-xs-12" ng-hide="stats_per_servers.length == 0">
            <div class="box box-info">
                <div class="box-header">
                    <div class="box-title"><?php echo Lang::get('player.profile.section_titles.stats_per_server'); ?></div>
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
                                <th>First Seen</th>
                                <th>Last Seen</th>
                                <th>Wins</th>
                                <th>Losses</th>
                                <th width="100px">Playtime</th>
                                <th width="250px">Server</th>
                            </thead>

                            <tbody>
                                <tr ng-repeat="stat in stats_per_servers">
                                    <td>{{ stat.Score | number:0 }}</td>
                                    <td>{{ stat.Kills | number:0 }}</td>
                                    <td>{{ stat.Deaths | number:0 }}</td>
                                    <td>{{ stat.Headshots | number:0 }}</td>
                                    <td>{{ stat.Suicide | number:0 }}</td>
                                    <td>{{ stat.FirstSeenOnServerUnix * 1000 | date: 'medium': timezone: 'UTC' }}</td>
                                    <td>{{ stat.LastSeenOnServerUnix * 1000 | date: 'medium': timezone: 'UTC' }}</td>
                                    <td>{{ stat.Wins | number:0 }}</td>
                                    <td>{{ stat.Losses | number:0 }}</td>
                                    <td>{{ stat.Playtime }}</td>
                                    <td><span class="trim-server-name">{{ stat.ServerName }}</span></td>
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
                    <div class="box-title"><?php echo Lang::get('player.profile.section_titles.session'); ?></div>
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
