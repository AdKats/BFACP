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
                                <th ng-if="server.game.Name == 'BF4'" class="hidden-xs hidden-sm">Spectators</th>
                                <th class="hidden-xs hidden-sm">Queue</th>
                                <th>Map</th>
                                <th class="hidden-xs hidden-sm">Next Map</th>
                                <th>Round</th>
                                <th ng-if="server.game.Name == 'BF4' || server.game.Name == 'BFHL'">Time Left</th>
                                <th>Uptime</th>
                                <th>
                                    <div ng-switch on="server.mode.uri">
                                        <span ng-switch-when="SquadDeathMatch0">Kills Needed</span>
                                        <span ng-switch-when="TeamDeathMatch0">Kills Needed</span>
                                        <span ng-switch-when="RushLarge0">Starting Lives</span>
                                        <span ng-switch-default>Starting Tickets</span>
                                    </div>
                                </th>
                                <th ng-if="server.game.Name == 'BF4' || server.game.Name == 'BFHL'">Type</th>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>
                                        <span ng-bind="server.players.online"></span> /
                                        <span ng-bind="server.players.max"></span>
                                    </td>
                                    <td ng-if="server.game.Name == 'BF4'" class="hidden-xs hidden-sm" ng-bind="server.players.spectators"></td>
                                    <td class="hidden-xs hidden-sm" ng-bind="server.players.queue"></td>
                                    <td>
                                        <span ng-bind="server.map.name"></span> /
                                        <span ng-bind="server.mode.name"></span>
                                    </td>
                                    <td class="hidden-xs hidden-sm">
                                        <span ng-bind="server.map.next.map.name"></span> /
                                        <span ng-bind="server.map.next.mode.name"></span>
                                    </td>
                                    <td ng-bind="momentDuration(server.times.round.seconds, 'seconds')"></td>
                                    <td ng-bind="momentDuration(server.times.remaining.seconds, 'seconds')" ng-if="server.game.Name == 'BF4' || server.game.Name == 'BFHL'"></td>
                                    <td ng-bind="momentDuration(server.times.uptime.seconds, 'seconds')"></td>
                                    <td ng-bind="server.tickets_starting | number"></td>
                                    <td ng-if="server.game.Name == 'BF4' || server.game.Name == 'BFHL'" ng-bind="server.type"></td>
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
        <div class="col-xs-12 col-md-6">
            <div class="box box-success">
                <div class="box-header">
                    <i class="fa fa-comments-o"></i>
                    <h3 class="box-title">Chat <span class="badge bg-green" ng-bind="messages.length"></span></h3>
                    <div class="box-tools pull-right">
                        <input class="form-control" type="text" ng-model="search.chat" placeholder="Filter Chat..." />
                    </div>
                </div>

                <div class="box-body chat scoreboard-chat" id="chat-box">
                    <div class="item" ng-repeat="(key, message) in messages | filter: { logSoldierName: search.chat } | orderBy: 'logDate': true track by message.ID">
                        <img ng-if="message.player.battlelog === null" src="https://www.gravatar.com/avatar/?s=128&d=mm&r=x" width="128" class="online" />
                        <img ng-if="message.player.battlelog !== null" ng-src="{{ message.player.battlelog.gravatar_img }}" width="128" class="online" />
                        <p class="message">
                            <a ng-href="{{ message.player.profile_url }}" target="_blank" class="name">
                                <small class="text-muted pull-right" tooltip="{{ moment(message.stamp).format('h:mm:ss a') }}" tooltip-placement="left">
                                    <i class="fa fa-clock-o"></i>
                                    <span ng-bind="moment(message.stamp).fromNow()"></span>
                                </small>
                                <small ng-class="message.class_css" ng-bind="message.logSubset"></small>
                                <span ng-bind="message.logSoldierName"></span>
                            </a>

                            <span ng-bind="message.logMessage"></span>
                        </p>
                    </div>
                </div>

                <?php if($bfacp->isLoggedIn && $bfacp->user->ability(null, 'admin.scoreboard.say')) : ?>
                <div class="box-footer">
                    <div class="input-group">
                        <input type="text" ng-model="chat.message" placeholder="Type message..." class="form-control" ng-disabled="chat.sending" ng-enter="admin.sendMessage()">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-danger btn-flat" ng-click="admin.sendMessage()" ng-disabled="chat.sending">
                                <ng-switch on="chat.sending">
                                    <span ng-switch-when="true"><i class="fa fa-cog fa-spin"></i> Sending...</span>
                                    <span ng-switch-default>Send</span>
                                </ng-switch>
                            </button>
                        </span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($bfacp->isLoggedIn): ?>
        <div class="col-xs-3 col-sm-2" ng-if="admins !== null">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Admins</h3>
                </div>

                <div class="box-body">
                    <ul class="list-unstyled">
                        <li ng-repeat="(key, player) in admins track by player.name">
                            <a ng-href="{{ player._player.profile_url }}" ng-bind="player.name" target="_blank"></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div ng-if="(server.game.Name == 'BF4' || server.game.Name == 'BFHL') && neutral.spectators" class="col-xs-3 col-<?php if($bfacp->isLoggedIn):?>sm-2<?php else:?>md-3<?php endif;?>" >
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Spectators</h3>
                    <div class="box-tools pull-right">
                        <span ng-if="neutral.spectators" class="badge bg-light-blue" ng-bind="neutral.spectators.length"></span>
                    </div>
                </div>

                <div class="box-body">
                    <ul class="list-unstyled">
                        <li ng-repeat="(key, player) in neutral.spectators track by player.name">
                            <a ng-href="{{ player._player.profile_url }}" ng-bind="player.name" target="_blank"></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div ng-if="neutral.players" class="col-xs-3 col-<?php if($bfacp->isLoggedIn):?>sm-2<?php else:?>md-3<?php endif;?>">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Joining</h3>
                    <div class="box-tools pull-right">
                        <span ng-if="neutral.players" class="badge bg-light-blue" ng-bind="neutral.players.length"></span>
                    </div>
                </div>

                <div class="box-body">
                    <ul class="list-unstyled">
                        <li ng-repeat="(key, player) in neutral.players track by player.name">
                            <i class="fa fa-circle-o-notch fa-spin"></i>
                            <a ng-href="{{ player._player.profile_url }}" ng-bind="player.name" target="_blank"></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                <input class="form-control" type="text" ng-model="search.scoreboard" placeholder="Search for player..." />
            </div>
        </div>
    </div>

    <?php if(!is_null($adminview)) { echo $adminview; } ?>

    <div class="row">
        <div class="col-xs-12 col-md-6" ng-repeat="(teamID, team) in teams track by teamID">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">
                        <span ng-bind="team.team.full_name || team.team"></span>
                        (<span ng-bind="team.players.length"></span>)
                        <i class="fa fa-trophy" ng-class="{'text-success': winning[teamID]  }"></i>
                    </h3>

                    <div class="box-tools pull-right" ng-if="server.mode.uri != 'CaptureTheFlag0'">
                        <span class="badge bg-light-blue">
                            <div ng-switch on="(server.mode.uri == 'RushLarge0') || (server.mode.uri == 'Heist0')">
                                <div ng-switch-when="true">
                                    <span ng-if="teamID == 2">&infin;</span>
                                    <span ng-if="teamID != 2" ng-bind="team.score | number"></span>
                                </div>
                                <span ng-switch-default ng-bind="team.score | number"></span>
                            </div>
                        </span>
                    </div>
                </div>

                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-condensed table-striped table-hover scoreboard">
                            <thead>
                                <?php if ($bfacp->isLoggedIn && !is_null($adminview)): ?>
                                <th>
                                    <input type="checkbox" ng-click="selectAll($event)" />
                                </th>
                                <?php endif;?>
                                <th ng-click="colSort('name')">
                                    <i ng-class="colSortClass('name')"></i>&nbsp;Name
                                </th>
                                <th ng-click="colSort('score')">
                                    <i ng-class="colSortClass('score')"></i>&nbsp;Score
                                </th>
                                <th>
                                    <span ng-click="colSort('kills')">
                                        <i ng-class="colSortClass('kills')"></i>&nbsp;K
                                    </span> /
                                    <span ng-click="colSort('deaths')">
                                        <i ng-class="colSortClass('deaths')"></i>&nbsp;D
                                    </span>
                                </th>
                                <th class="visible-lg" ng-click="colSort('kd')">
                                    <i ng-class="colSortClass('kd')"></i>&nbsp;KD Ratio
                                </th>
                                <th ng-click="colSort('squadId')" class="hidden-xs hidden-sm">
                                    <i ng-class="colSortClass('squadId')"></i>&nbsp;Squad
                                </th>
                                <th ng-if="server.game.Name == 'BF4' || server.game.Name == 'BFHL'" ng-click="colSort('ping')">
                                    <i ng-class="colSortClass('ping')"></i>&nbsp;Ping
                                </th>
                            </thead>

                            <tbody>
                                <tr ng-repeat="(key, player) in team.players | filter: { name: search.scoreboard } | orderBy:sort.column:sort.desc track by player.name">
                                    <?php if ($bfacp->isLoggedIn && !is_null($adminview)): ?>
                                    <td>
                                        <input type="checkbox" name="players" ng-value="player.name" ng-click="isSelectAll($event); updateSelectedPlayers()" />
                                    </td>
                                    <?php endif;?>
                                    <td>
                                        <img ng-src="{{ player._player.rank_image }}" width="24px" tooltip="Rank {{ player.rank }}" class="hidden-xs hidden-sm">
                                        <img ng-src="{{ player._player.country_flag }}" width="24px" tooltip="{{ player._player.country_name }}" class="hidden-xs hidden-sm">
                                        <span ng-if="player._player.ClanTag">
                                            [<span ng-bind="player._player.ClanTag"></span>]
                                        </span>
                                        <a ng-href="{{ player._player.profile_url }}" ng-bind="player.name" target="_blank"></a>
                                    </td>
                                    <td ng-bind="player.score | number"></td>
                                    <td>
                                        <span ng-bind="player.kills"></span> /
                                        <span ng-bind="player.deaths"></span>
                                    </td>
                                    <td class="visible-lg" ng-bind="player.kd"></td>
                                    <td class="hidden-xs hidden-sm">
                                        <i class="fa fa-lock" ng-if="player.isSquadLocked">&nbsp;</i>
                                        <span ng-bind="player.squadName"></span>
                                    </td>
                                    <td ng-if="server.game.Name == 'BF4' || server.game.Name == 'BFHL'" ng-bind="player.ping || '--'" ng-class="pingColor(player.ping)"></td>
                                </tr>
                            </tbody>

                            <tfoot>
                                <tr>
                                    <td colspan="<?php echo !$bfacp->isLoggedIn ? 1 : 2;?>">
                                        <span class="pull-right">Total</span>
                                    </td>
                                    <td ng-bind="sum(team.players, 'score') | number"></td>
                                    <td>
                                        <span ng-bind="sum(team.players, 'kills') | number"></span> /
                                        <span ng-bind="sum(team.players, 'deaths') | number"></span>
                                    </td>
                                    <td class="visible-lg" ng-bind="avg(team.players, 'kd', 2)"></td>
                                    <td class="hidden-xs hidden-sm" ng-if="server.game.Name == 'BF4' || server.game.Name == 'BFHL'">
                                        <span class="pull-right">Average Ping</span>
                                    </td>
                                    <td  ng-if="server.game.Name == 'BF4' || server.game.Name == 'BFHL'"
                                        ng-class="pingColor(avg(team.players, 'ping'))" ng-bind="avg(team.players, 'ping') | number"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="box-footer clearfix" ng-if="team.commander">
                    <table class="table table-condensed">
                        <caption class="text-center">
                            <h3>Commander</h3>
                        </caption>

                        <thead>
                            <th>&nbsp;</th>
                            <th>Name</th>
                            <th>Score</th>
                            <th>K/D</th>
                            <th class="visible-lg">KD Ratio</th>
                            <th ng-if="server.game.Name == 'BF4' || server.game.Name == 'BFHL'">Ping</th>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    <input type="checkbox" name="chkplayers" value="{{ team.commander.name }}" />
                                </td>
                                <td>
                                    <span ng-bind="team.commander.name"></span>
                                </td>
                                <td ng-bind="team.commander.score | number"></td>
                                <td>
                                    <span ng-bind="team.commander.kills"></span> /
                                    <span ng-bind="team.commander.deaths"></span>
                                </td>
                                <td class="visible-lg" ng-bind="kd(team.commander.kills, team.commander.deaths)"></td>
                                <td ng-if="server.game.Name == 'BF4' || server.game.Name == 'BFHL'" ng-bind="team.commander.ping || '--'" ng-class="pingColor(team.commander.ping)"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="clearfix visible-sm-block" ng-if="teamID%2 === 0"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-body">
                    <div id="round-graph" style="width: 99%"></div>
                </div>
            </div>
        </div>
    </div>

</section>
