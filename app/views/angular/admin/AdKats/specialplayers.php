<div class="row" ng-controller="PlayerList">
    <div class="col-xs-12">
        <div class="box box-info" >
            <div class="box-header">
                <div class="box-title">
                    Listing
                </div>
                <div class="box-tools pull-right">
                    <button ng-click="firstPage()" class="btn btn-primary btn-xs" ng-class="{'disabled': main.page==1}">First</button>
                    <button ng-click="previousPage()" class="btn btn-primary btn-xs" ng-class="{'disabled': main.page==1}">Previous</button>
                    <button ng-click="nextPage()" class="btn btn-primary btn-xs" ng-class="{'disabled': main.page==main.pages}">Next</button>
                    <button ng-click="lastPage()" class="btn btn-primary btn-xs" ng-class="{'disabled': main.page==main.pages}">Last</button>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-condensed table-striped">
                    <thead>
                        <th>ID</th>
                        <th width="200px">Group</th>
                        <th width="250px" class="hidden-sm">Server</th>
                        <th>Player/Identity</th>
                        <th>Effective</th>
                        <th>Expires</th>
                    </thead>

                    <tbody>
                        <tr ng-repeat="player in players track by player.specialplayer_id">
                            <td>{{ player.specialplayer_id }}</td>
                            <td>{{ player.player_group }}</td>
                            <td class="hidden-sm" ng-switch on="player.server !== null">
                                <div ng-switch-when="true">
                                    <span class="trim-server-name" tooltip-placement="top" tooltip="{{ player.server.ServerName }}">
                                        {{ player.server.ServerName }}
                                    </span>
                                </div>
                                <div ng-switch-default>
                                    No Server
                                </div>
                            </td>
                            <td ng-switch on="player.player !== null">
                                <div ng-switch-when="true">
                                    <a ng-href="/player/{{ player.player.PlayerID }}/{{ player.player.SoldierName }}" target="_blank">
                                        {{ player.player.SoldierName }}
                                    </a>
                                </div>
                                <div ng-switch-default>
                                    {{ player.player_identifier }}
                                </div>
                            </td>
                            <td>{{ formatDate(player.player_effective_local) }}</td>
                            <td>{{ formatDate(player.player_expiration_local) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="box-footer clearfix">
                <div class="pull-left">
                    Showing {{main.from | number}} to {{main.to | number}} out of {{main.total | number}} - Page {{main.page | number}} of {{main.pages | number}}
                </div>
                <div class="pull-right">
                    <button ng-click="firstPage()" class="btn btn-primary btn-xs" ng-class="{'disabled': main.page==1}">First</button>
                    <button ng-click="previousPage()" class="btn btn-primary btn-xs" ng-class="{'disabled': main.page==1}">Previous</button>
                    <button ng-click="nextPage()" class="btn btn-primary btn-xs" ng-class="{'disabled': main.page==main.pages}">Next</button>
                    <button ng-click="lastPage()" class="btn btn-primary btn-xs" ng-class="{'disabled': main.page==main.pages}">Last</button>
                </div>
            </div>

            <div class="overlay" ng-show="isLoading"></div>
            <div class="loading-img" ng-show="isLoading"></div>
        </div>
    </div>
</div>
