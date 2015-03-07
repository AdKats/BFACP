<section ng-hide="!loading">

    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">
                        <label ng-class="server.game.class_css" ng-bind="server.game.Name"></label>
                        <span ng-bind="server.name"></span>
                    </h3>
                    <div class="box-tools pull-right">
                        <i class="fa fa-refresh" ng-class="{'fa-spin': refresh}"></i>
                    </div>
                </div>

                <div class="box-body">
                    <img class="img-responsive center-block hidden-xs" ng-src="{{ server.map.images.wide }}" alt="{{ server.map.name }}">
                    <div class="table-responsive">
                        <table class="table table-condensed">
                            <caption class="hidden-xs">
                                <p ng-bind="server.description"></p>
                            </caption>

                            <thead>
                                <th>Online</th>
                                <th class="hidden-xs hidden-sm">Spectators</th>
                                <th class="hidden-xs hidden-sm">Queue</th>
                                <th>Map</th>
                                <th class="hidden-xs hidden-sm">Next Map</th>
                                <th>Round</th>
                                <th ng-if="server.game.Name == 'BF4'">Time Left</th>
                                <th>Uptime</th>
                                <th>Type</th>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>
                                        <span ng-bind="server.players.online"></span> /
                                        <span ng-bind="server.players.max"></span>
                                    </td>
                                    <td class="hidden-xs hidden-sm" ng-bind="server.players.spectators"></td>
                                    <td class="hidden-xs hidden-sm" ng-bind="server.players.queue"></td>
                                    <td>
                                        <span ng-bind="server.map.name"></span> /
                                        <span ng-bind="server.mode.name"></span>
                                    </td>
                                    <td class="hidden-xs hidden-sm">
                                        <span ng-bind="server.map.next.map.name"></span> /
                                        <span ng-bind="server.map.next.mode.name"></span>
                                    </td>
                                    <td ng-bind="server.times.round.humanize"></td>
                                    <td ng-bind="server.times.remaining.humanize" ng-if="server.game.Name == 'BF4'"></td>
                                    <td ng-bind="server.times.uptime.humanize"></td>
                                    <td ng-bind="server.type"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="overlay" ng-if="refresh && server.length === 0">
                    <i class="fa fa-refresh fa-spin"></i>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div ng-repeat="(teamID, team) in teams track by teamID" ng-if="teamID > 0" class="col-xs-12 col-sm-6">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">
                        <span ng-bind="team.team.full_name"></span>
                        (<span ng-bind="team.players.length"></span>)
                    </h3>

                    <div class="box-tools pull-right" ng-if="server.mode.uri != 'CaptureTheFlag0'">
                        <span class="badge bg-light-blue" ng-bind="team.score | number"></span>
                    </div>
                </div>

                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-condensed table-striped table-hover">
                            <thead>
                                <th>
                                    <input type="checkbox" id="chk_{{ teamID }}">
                                </th>
                                <th>Name</th>
                                <th>Score</th>
                                <th>K/D</th>
                                <th class="visible-lg">KD Ratio</th>
                                <th class="hidden-xs hidden-sm">Squad</th>
                                <th ng-if="server.game.Name == 'BF4'">Ping</th>
                            </thead>

                            <tbody>
                                <tr ng-repeat="(key, player) in team.players | orderBy: 'score':true track by player.name">
                                    <td>
                                        <input type="checkbox" name="chkplayers" value="{{ player.name }}" />
                                    </td>
                                    <td>
                                        <img ng-src="{{ player._player.rank_image }}" width="24px" tooltip="Rank {{ player.rank }}" class="hidden-xs hidden-sm">
                                        <span ng-bind="player._player.ClanTag"></span>
                                        <a ng-href="{{ player._player.profile_url }}" ng-bind="player.name" target="_blank"></a>
                                    </td>
                                    <td ng-bind="player.score | number"></td>
                                    <td>
                                        <span ng-bind="player.kills"></span> /
                                        <span ng-bind="player.deaths"></span>
                                    </td>
                                    <td class="visible-lg" ng-bind="kd(player.kills, player.deaths)"></td>
                                    <td class="hidden-xs hidden-sm">
                                        <i class="fa fa-lock" ng-if="player.isSquadLocked">&nbsp;</i>
                                        <span ng-bind="player.squadName"></span>
                                    </td>
                                    <td ng-if="server.game.Name == 'BF4'" ng-bind="player.ping"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>
